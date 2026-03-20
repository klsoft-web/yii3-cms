<?php

namespace App\Admin\Web\Page;

use App\Admin\Data\Events\EntityChanged;
use App\Admin\Data\Post\PostAdminRepositoryInterface;
use App\Admin\Data\Role\RoleRepositoryInterface;
use App\Admin\Data\Slug\SlugAdminRepositoryInterface;
use App\Admin\Web\Shared\Form\DeleteEntitiesForm;
use Klsoft\Yii3CmsCore\Data\Entities\Meta;
use Klsoft\Yii3CmsCore\Data\Entities\Post;
use Klsoft\Yii3CmsCore\Data\Entities\Slug;
use Klsoft\Yii3CmsCore\Data\Post\PostStatus;
use Klsoft\Yii3CmsCore\Data\Post\PostType;
use App\Data\Rbac\Permission as RbacPermission;
use App\Data\User\UserRepositoryInterface;
use App\Messages\App;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Klsoft\Yii3DataReaderDoctrine\Filter\ObjectEquals;
use Psr\EventDispatcher\EventDispatcherInterface;
use Klsoft\Yii3Authz\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Klsoft\Yii3DataReaderDoctrine\DoctrineDataReader;
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

final class PageController
{
    public function __construct(
        private readonly EntityManagerInterface       $entityManager,
        private readonly PostAdminRepositoryInterface $postRepository,
        private readonly SlugAdminRepositoryInterface $slugRepository,
        private readonly UserRepositoryInterface      $userRepository,
        private readonly RoleRepositoryInterface      $roleRepository,
        private readonly EventDispatcherInterface     $eventDispatcher,
        private readonly CurrentUser                  $user,
        private readonly FormHydrator                 $formHydrator,
        private readonly TranslatorInterface          $translator,
        private readonly UrlGeneratorInterface        $urlGenerator,
        private readonly CurrentRoute                 $currentRoute,
        private readonly ResponseFactoryInterface     $responseFactory,
        private WebViewRenderer                       $viewRenderer)
    {
        $this->viewRenderer = $this->viewRenderer
            ->withLayout('@src/Admin/Web/Shared/Layout/Main/layout.php');
    }

    #[Permission(RbacPermission::CREATE_PAGE . '|' . RbacPermission::UPDATE_PAGE . '|' . RbacPermission::DELETE_PAGE)]
    public function list(): ResponseInterface
    {
        return $this->viewRenderer
            ->render(
                __DIR__ . '/list_template',
                [
                    'dataReader' => (new DoctrineDataReader(
                        $this->entityManager,
                        Post::class))
                        ->withFilter(new ObjectEquals('post_type', PostType::Page))
                        ->withSort(Sort::any(['name'])->withOrder(['name' => 'asc'])),
                    'canUserCreatePage' => $this->user->can(RbacPermission::CREATE_PAGE),
                    'canUserUpdatePage' => $this->roleRepository->userHasPermission($this->user->getId(), RbacPermission::UPDATE_PAGE),
                    'canUserDeletePage' => $this->user->can(RbacPermission::DELETE_PAGE)
                ]
            );
    }

    #[Permission(RbacPermission::CREATE_PAGE)]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return $this->edit($request);
    }

    #[Permission(
        RbacPermission::UPDATE_PAGE,
        ['entity' =>
            [
                '__container_entry_identifier',
                PageController::class,
                'findPostByRouteArgument'
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
    public function findPostByRouteArgument(): Post
    {
        return $this->findPost($this->currentRoute->getArgument('id'));
    }

    private function edit(
        ServerRequestInterface         $request,
        #[RouteArgument('id')] ?string $id = null): ResponseInterface
    {
        $form = new PageForm(
            $this->slugRepository,
            $this->translator);
        try {
            $post = $id !== null ? $this->findPost($id) : new Post();
            if ($this->formHydrator->populateFromPost($form, $request)) {
                if ($this->formHydrator->validate($form)->isValid() &&
                    $this->validate($id, $post, $form)) {
                    if ($post->getSlug() === null) {
                        $post->setSlug($this->createSlug($form));
                        $post->setPostType(PostType::Page);
                        $post->setDateTime(new DateTimeImmutable());
                        $post->setCreatedByUser($this->userRepository->find($this->user->getId()));
                    }
                    $post->setName($form->name);
                    $post->setContent($form->content);
                    $post->setStatus(PostStatus::from($form->status));
                    if ($form->description !== '') {
                        $metaItems = $post->getMetaItems();
                        $metaItemsSize = count($metaItems);
                        if ($metaItemsSize > 0) {
                            for ($i = 0; $i < $metaItemsSize; $i++) {
                                $metaItem = $metaItems[$i];
                                if ($metaItem->getName() === 'description') {
                                    $metaItem->setContent($form->description);
                                    $metaItems[$i] = $metaItem;
                                }
                            }
                            $post->setMetaItems($metaItems);
                        } else {
                            $meta = new Meta();
                            $meta->setName('description');
                            $meta->setContent($form->description);
                            $post->setMetaItems(new ArrayCollection([$meta]));
                        }
                    }

                    $entityChangedResult = $post->getSlug()->getId() !== $form->slug ? $this->postRepository->updateSlug($post, $this->createSlug($form), []) : $this->postRepository->save($post, $form->description === '', []);
                    $this->eventDispatcher->dispatch(new EntityChanged($entityChangedResult->entity, $entityChangedResult->eventType, $this->user->getId()));

                    return $this->responseFactory->createResponse()
                        ->withStatus(Status::FOUND)
                        ->withHeader('Location', $this->urlGenerator->generate('admin/pages'));
                }
            } else {
                if ($this->validate($id, $post, $form) &&
                    $post->getSlug() !== null) {
                    $form->id = $post->getSlug()->getId();
                    $form->slug = $post->getSlug()->getId();
                    $form->name = $post->getName();
                    $form->content = $post->getContent();
                    $form->status = $post->getStatus()->value;
                    foreach ($post->getMetaItems() as $metaItem) {
                        if ($metaItem->getName() === 'description') {
                            $form->description = $metaItem->getContent();
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
    private function findPost(string $id): ?Post
    {
        $slug = $this->slugRepository->find($id);
        if ($slug !== null) {
            return $this->postRepository->find($slug);
        }
        return null;
    }

    private function validate(
        ?string  $id,
        Post     $post,
        PageForm $form): bool
    {
        if ($id !== null &&
            ($post->getSlug() === null || $post->getPostType() !== PostType::Page)) {
            $form->addError($this->translator->translate(App::THE_RECORD_WAS_NOT_FOUND));
            return false;
        }
        return true;
    }

    private function createSlug(PageForm $form): Slug
    {
        $slug = new Slug();
        $slug->setId($form->slug);
        $slug->setEntityClass(Post::class);
        $this->slugRepository->save($slug);

        return $slug;
    }

    #[Permission(RbacPermission::DELETE_PAGE)]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $form = new DeleteEntitiesForm();
            if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
                $slugs = [];
                foreach ($form->delete_ids as $id) {
                    $slug = $this->slugRepository->find($id);
                    if ($slug !== null) {
                        $slugs[] = $slug;
                    }
                }
                $entityChangedResultList = $this->postRepository->delete($slugs);
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
