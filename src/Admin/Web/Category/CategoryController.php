<?php

namespace App\Admin\Web\Category;

use App\Admin\Data\Category\CategoryAdminRepositoryInterface;
use App\Admin\Data\Events\EntityChanged;
use App\Admin\Data\Role\RoleRepositoryInterface;
use App\Admin\Data\Slug\SlugAdminRepositoryInterface;
use App\Admin\Web\Shared\Form\DeleteEntitiesForm;
use App\Data\Entities\Category;
use App\Data\Entities\Meta;
use App\Data\Entities\Slug;
use App\Data\Rbac\Permission as RbacPermission;
use App\Data\User\UserRepositoryInterface;
use App\Messages\App;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Klsoft\Yii3Authz\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Klsoft\Yii3DataReaderDoctrine\DoctrineDataReader;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TheSeer\Tokenizer\Exception;
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

final class CategoryController
{
    public function __construct(
        private readonly EntityManagerInterface           $entityManager,
        private readonly CategoryAdminRepositoryInterface $categoryRepository,
        private readonly SlugAdminRepositoryInterface     $slugRepository,
        private readonly UserRepositoryInterface          $userRepository,
        private readonly RoleRepositoryInterface          $roleRepository,
        private readonly CurrentUser                      $user,
        private readonly EventDispatcherInterface         $eventDispatcher,
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

    #[Permission(RbacPermission::CREATE_CATEGORY . '|' . RbacPermission::UPDATE_CATEGORY . '|' . RbacPermission::DELETE_CATEGORY)]
    public function list(): ResponseInterface
    {
        /** @var string $userId */
        $userId = $this->user->getId();

        return $this->viewRenderer
            ->render(
                __DIR__ . '/list_template',
                [
                    'dataReader' => (new DoctrineDataReader(
                        $this->entityManager,
                        Category::class))
                        ->withSort(Sort::any(['name', 'order'])->withOrder(['name' => 'asc'])),
                    'canUserCreateCategory' => $this->user->can(RbacPermission::CREATE_CATEGORY),
                    'canUserUpdateCategory' => $this->roleRepository->userHasPermission($userId, RbacPermission::UPDATE_CATEGORY),
                    'canUserDeleteCategory' => $this->user->can(RbacPermission::DELETE_CATEGORY)
                ]
            );
    }

    #[Permission(RbacPermission::CREATE_CATEGORY)]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return $this->edit($request);
    }

    #[Permission(
        RbacPermission::UPDATE_CATEGORY,
        ['entity' =>
            [
                '__container_entry_identifier',
                CategoryController::class,
                'findCategoryByRouteArgument'
            ]
        ])
    ]
    public function update(
        ServerRequestInterface        $request,
        #[RouteArgument('id')] string $id): ResponseInterface
    {
        return $this->edit($request, $id);
    }

    /**
     * @throws Throwable
     */
    public function findCategoryByRouteArgument(): Category
    {
        $id = $this->currentRoute->getArgument('id');
        if ($id !== null) {
            return $this->findCategory($id);
        }
        throw new Exception($this->translator->translate(App::THE_RECORD_WAS_NOT_FOUND));
    }

    private function edit(
        ServerRequestInterface $request,
        ?string                $id = null): ResponseInterface
    {
        $form = new CategoryForm(
            $this->slugRepository,
            $this->translator);
        try {
            $category = $id !== null ? $this->findCategory($id) : new Category();
            if ($this->formHydrator->populateFromPost($form, $request)) {
                if ($this->formHydrator->validate($form)->isValid() &&
                    $this->validate($id, $category, $form)) {
                    /** @var int $userId */
                    $userId = $this->user->getId();
                    if ($category->getSlug() === null) {
                        $category->setSlug($this->createSlug($form));
                        $user = $this->userRepository->find($userId);
                        if ($user !== null) {
                            $category->setCreatedByUser($user);
                        }
                    }
                    $category->setName($form->name);
                    $category->setSummary($form->summary);
                    $category->setSummaryImgPath($form->summary_img_path);
                    $category->setOrder($form->order);
                    if ($form->description !== '') {
                        /** @var array<Meta> $metaItems */
                        $metaItems = $category->getMetaItems();
                        $metaItemsSize = count($metaItems);
                        if ($metaItemsSize > 0) {
                            for ($i = 0; $i < $metaItemsSize; $i++) {
                                $metaItem = $metaItems[$i];
                                if ($metaItem->getName() === 'description') {
                                    $metaItem->setContent($form->description);
                                    $metaItems[$i] = $metaItem;
                                }
                            }
                            /** @var Collection $collection */
                            $collection = new ArrayCollection($metaItems);
                            $category->setMetaItems($collection);
                        } else {
                            $meta = new Meta();
                            $meta->setName('description');
                            $meta->setContent($form->description);
                            /** @var Collection $collection */
                            $collection = new ArrayCollection([$meta]);
                            $category->setMetaItems($collection);
                        }
                    }

                    $entityChangedResult = $category->getSlug()?->getId() !== $form->slug ? $this->categoryRepository->updateSlug($category, $this->createSlug($form)) : $this->categoryRepository->save($category);
                    $this->eventDispatcher->dispatch(new EntityChanged($entityChangedResult->entity, $entityChangedResult->eventType, $userId));

                    return $this->responseFactory->createResponse()
                        ->withStatus(Status::FOUND)
                        ->withHeader('Location', $this->urlGenerator->generate('admin/categories'));
                }
            } else {
                if ($this->validate($id, $category, $form)) {
                    $categorySlug = $category->getSlug();
                    if ($categorySlug !== null) {
                        $form->id = $categorySlug->getId();
                        $form->slug = $categorySlug->getId();
                        $form->summary = $category->getSummary() ?? '';
                        $form->summary_img_path = $category->getSummaryImgPath();
                        $form->name = $category->getName();
                        $form->order = $category->getOrder();
                        /** @var Meta $metaItem */
                        foreach ($category->getMetaItems() as $metaItem) {
                            if ($metaItem->getName() === 'description') {
                                $form->description = $metaItem->getContent();
                            }
                        }
                    }
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

    /**
     * @throws Throwable
     */
    private function findCategory(string $id): Category
    {
        $category = null;
        $slug = $this->slugRepository->find($id);
        if ($slug !== null) {
            $category = $this->categoryRepository->find($slug);
        }
        if ($category !== null) {
            return $category;
        }
        throw new Exception($this->translator->translate(App::THE_RECORD_WAS_NOT_FOUND));
    }

    private function validate(
        ?string      $id,
        Category     $category,
        CategoryForm $form): bool
    {
        if ($id !== null &&
            $category->getSlug() === null) {
            $form->addError($this->translator->translate(App::THE_RECORD_WAS_NOT_FOUND));
            return false;
        }
        return true;
    }

    private function createSlug(CategoryForm $form): Slug
    {
        $slug = new Slug();
        $slug->setId($form->slug);
        $slug->setEntityClass(Category::class);
        $this->slugRepository->save($slug);

        return $slug;
    }

    #[Permission(RbacPermission::DELETE_CATEGORY)]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $form = new DeleteEntitiesForm();
            if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
                $slugs = [];
                foreach ($form->delete_ids as $id) {
                    $slug = $this->slugRepository->find((string)$id);
                    if ($slug !== null) {
                        $slugs[] = $slug;
                    }
                }
                $entityChangedResultList = $this->categoryRepository->delete($slugs);
                /** @var int $userId */
                $userId = $this->user->getId();
                foreach ($entityChangedResultList as $entityChangedResult) {
                    $this->eventDispatcher->dispatch(new EntityChanged($entityChangedResult->entity, $entityChangedResult->eventType, $userId));
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
