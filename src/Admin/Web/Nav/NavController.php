<?php

namespace App\Admin\Web\Nav;

use App\Admin\Data\Category\CategoryAdminRepositoryInterface;
use App\Admin\Data\Events\EntityChanged;
use App\Admin\Data\Nav\NavAdminRepositoryInterface;
use App\Admin\Data\Post\PostAdminRepositoryInterface;
use App\Admin\Data\Role\RoleRepositoryInterface;
use App\Admin\Web\Shared\Form\DeleteEntitiesForm;
use App\Data\Entities\Nav;
use App\Data\Entities\NavItem;
use App\Data\Nav\NavItemType;
use App\Data\Nav\NavPosition;
use App\Data\Rbac\Permission as RbacPermission;
use App\Data\User\UserRepositoryInterface;
use App\Messages\App;
use Klsoft\Yii3Authz\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Klsoft\Yii3DataReaderDoctrine\DoctrineDataReader;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Status;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class NavController
{
    public function __construct(
        private readonly EntityManagerInterface           $entityManager,
        private readonly NavAdminRepositoryInterface      $navRepository,
        private readonly PostAdminRepositoryInterface     $postRepository,
        private readonly CategoryAdminRepositoryInterface $categoryRepository,
        private readonly UserRepositoryInterface          $userRepository,
        private readonly RoleRepositoryInterface          $roleRepository,
        private readonly EventDispatcherInterface         $eventDispatcher,
        private readonly CurrentUser                      $user,
        private readonly FormHydrator                     $formHydrator,
        private readonly TranslatorInterface              $translator,
        private readonly UrlGeneratorInterface            $urlGenerator,
        private readonly CurrentRoute                     $currentRoute,
        private readonly ResponseFactoryInterface         $responseFactory,
        private WebViewRenderer                           $viewRenderer)
    {
        $this->viewRenderer = $this->viewRenderer
            ->withLayout('@src/Admin/Web/Shared/Layout/Main/layout.php');
    }

    #[Permission(RbacPermission::CREATE_NAVIGATION . '|' . RbacPermission::UPDATE_NAVIGATION . '|' . RbacPermission::DELETE_NAVIGATION)]
    public function list(): ResponseInterface
    {
        return $this->viewRenderer
            ->render(
                __DIR__ . '/list_template',
                [
                    'dataReader' => (new DoctrineDataReader(
                        $this->entityManager,
                        Nav::class,
                        ['id', 'name', 'position', 'order']))
                        ->withSort(Sort::any(['name', 'position', 'order'])->withOrder(['name' => 'asc'])),
                    'canUserCreateNav' => $this->user->can(RbacPermission::CREATE_NAVIGATION),
                    'canUserUpdateNav' => $this->roleRepository->userHasPermission($this->user->getId(), RbacPermission::UPDATE_NAVIGATION),
                    'canUserDeleteNav' => $this->user->can(RbacPermission::DELETE_NAVIGATION)
                ]
            );
    }

    #[Permission(RbacPermission::CREATE_NAVIGATION)]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return $this->edit($request);
    }

    #[Permission(
        RbacPermission::UPDATE_PAGE,
        ['entity' =>
            [
                '__container_entry_identifier',
                NavController::class,
                'findNavByRouteArgument'
            ]
        ])
    ]
    public function update(
        ServerRequestInterface     $request,
        #[RouteArgument('id')] int $id): ResponseInterface
    {
        return $this->edit($request, $id);
    }

    /**
     * @throws Throwable
     */
    public function findNavByRouteArgument(): Nav
    {
        return $this->entityManager->find(Nav::class, $this->currentRoute->getArgument('id'));
    }

    private function edit(
        ServerRequestInterface      $request,
        #[RouteArgument('id')] ?int $id = null): ResponseInterface
    {
        $form = new NavForm(
            $this->navRepository,
            $this->translator);
        try {
            $nav = $id !== null ? $this->navRepository->find($id) : new Nav();
            if ($this->formHydrator->populateFromPost($form, $request)) {
                if ($this->formHydrator->validate($form)->isValid() &&
                    $this->validate($id, $nav, $form)) {
                    $nav->setName($form->name);
                    $nav->setPosition(NavPosition::from($form->position));
                    $nav->setOrder($form->order);
                    if ($nav->getId() === null) {
                        $nav->setCreatedByUser($this->userRepository->find($this->user->getId()));
                    }

                    $navItems = [];
                    foreach ($form->nav_items as $value) {
                        $navItem = new NavItem();
                        if ($value['id'] !== '') {
                            $navItem = $this->navRepository->findNavItem($value['id']);
                        }
                        if ($navItem !== null) {
                            $navItem->setName($value['name']);
                            $navItem->setNavItemType(NavItemType::from($value['nav_item_type']));
                            $navItem->setValue($value['value']);
                            $navItem->setOrder($value['order']);
                            $navItems[] = $navItem;
                        }
                    }

                    $entityChangedResult = $this->navRepository->save($nav, $navItems);
                    $this->eventDispatcher->dispatch(new EntityChanged($entityChangedResult->entity, $entityChangedResult->eventType, $this->user->getId()));

                    return $this->responseFactory->createResponse()
                        ->withStatus(Status::FOUND)
                        ->withHeader('Location', $this->urlGenerator->generate('admin/navs'));
                }
            } else {
                if ($this->validate($id, $nav, $form) &&
                    $nav->getId() !== null) {
                    $form->id = $nav->getId();
                    $form->name = $nav->getName();
                    $form->position = $nav->getPosition()->value;
                    $form->order = $nav->getOrder();
                    $form->nav_items = $this->navRepository->getAllNavItemsByNav($nav);
                }
            }
        } catch (Throwable $throwable) {
            $form->addError($throwable->getMessage());

        }

        return $this->viewRenderer->render(
            __DIR__ . '/edit_template',
            [
                'form' => $form
            ]
        );
    }

    private function validate(
        ?string $id,
        Nav     $nav,
        NavForm $form): bool
    {
        if ($id !== null &&
            $nav->getId() === null) {
            $form->addError($this->translator->translate(App::THE_RECORD_WAS_NOT_FOUND));
            return false;
        }
        return true;
    }

    #[Permission(RbacPermission::CREATE_NAVIGATION . '|' . RbacPermission::UPDATE_NAVIGATION)]
    public function findEntities(ServerRequestInterface $request): ResponseInterface
    {
        $form = new FindEntitiesForm();
        $entities = [];
        if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
            if ($form->entity_type === 'Category') {
                $entities = array_map(fn($post) => ['id' => $post->getSlug()->getId(), 'name' => $post->getName()], $this->categoryRepository->findAllByNameLikeTo($form->search, 0, 5));
            } else {
                $entities = array_map(fn($post) => ['id' => $post->getSlug()->getId(), 'name' => $post->getName()], $this->postRepository->findAllPagesByNameLikeTo($form->search, 0, 5));
            }
        }
        return $this->viewRenderer->renderPartial(
            __DIR__ . '/found_entities_template',
            [
                'data' => $entities
            ]
        );
    }

    #[Permission(RbacPermission::CREATE_NAVIGATION . '|' . RbacPermission::UPDATE_NAVIGATION)]
    public function fetchNavItems(ServerRequestInterface $request): ResponseInterface
    {
        $form = new NavItemsForm();
        $navItems = [];
        if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
            foreach ($form->nav_items as $item) {
                $navItem = new NavItem();
                $navItem->setNavItemType($form->nav_item_type === AddNavItemType::Url->value ? NavItemType::Url : NavItemType::Slug);
                $navItem->setName($item['name']);
                $navItem->setValue($item['value']);
                $navItem->setOrder(1);
                $navItems[] = $navItem;
            }
        }
        return $this->viewRenderer->renderPartial(
            __DIR__ . '/nav_items_template',
            [
                'navItems' => $navItems
            ]
        );
    }

    #[Permission(RbacPermission::DELETE_NAVIGATION)]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $form = new DeleteEntitiesForm();
            if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
                $entityChangedResultList = $this->navRepository->delete($form->delete_ids);
                foreach ($entityChangedResultList as $entityChangedResult) {
                    $this->eventDispatcher->dispatch(new EntityChanged($entityChangedResult->entity, $entityChangedResult->eventType, $this->user->getId()));
                }
            }
            return $this->responseFactory->createResponse()->withStatus(Status::OK);
        } catch (Throwable $throwable) {
            $response = $this->responseFactory->createResponse(Status::INTERNAL_SERVER_ERROR);
            $response->getBody()->write($throwable->getMessage());
            return $response;
        }
    }
}
