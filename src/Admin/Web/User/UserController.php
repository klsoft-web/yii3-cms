<?php

namespace App\Admin\Web\User;

use App\Admin\Data\Events\EntityChanged;
use App\Admin\Data\User\UserAdminRepositoryInterface;
use App\Admin\Data\User\UserWithRoles;
use App\Admin\Web\Shared\Form\DeleteEntitiesForm;
use App\Data\Auth\AuthKeyRepositoryInterface;
use App\Data\Auth\AuthRepositoryInterface;
use App\Data\Entities\User;
use App\Data\Rbac\Permission as RbacPermission;
use App\Data\User\UserStatus;
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
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class UserController
{
    public function __construct(
        private readonly EntityManagerInterface       $entityManager,
        private readonly UserAdminRepositoryInterface $userRepository,
        private readonly AuthRepositoryInterface      $authRepository,
        private readonly AuthKeyRepositoryInterface   $authKeyRepository,
        private readonly PasswordHasher               $passwordHasher,
        private readonly CurrentUser                  $user,
        private readonly EventDispatcherInterface     $eventDispatcher,
        private readonly FormHydrator                 $formHydrator,
        private readonly TranslatorInterface          $translator,
        private readonly UrlGeneratorInterface        $urlGenerator,
        private readonly ResponseFactoryInterface     $responseFactory,
        private WebViewRenderer                       $viewRenderer)
    {
        $this->viewRenderer = $this->viewRenderer
            ->withLayout('@src/Admin/Web/Shared/Layout/Main/layout.php');
    }

    #[Permission(RbacPermission::CREATE_USER . '|' . RbacPermission::UPDATE_USER . '|' . RbacPermission::DELETE_USER)]
    public function list(): ResponseInterface
    {
        return $this->viewRenderer
            ->withLayout('@src/Admin/Web/Shared/Layout/Main/layout.php')
            ->render(
                __DIR__ . '/list_template',
                [
                    'dataReader' => (new DoctrineDataReader(
                        $this->entityManager,
                        User::class,
                        ['id', 'name', 'email', 'status']))
                        ->withSort(Sort::any(['name'])->withOrder(['name' => 'asc'])),
                    'canUserCreateUser' => $this->user->can(RbacPermission::CREATE_USER),
                    'canUserUpdateUser' => $this->user->can(RbacPermission::UPDATE_USER),
                    'canUserDeleteUser' => $this->user->can(RbacPermission::DELETE_USER)
                ]
            );
    }

    #[Permission(RbacPermission::CREATE_USER)]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return $this->edit($request);
    }

    #[Permission(RbacPermission::UPDATE_USER)]
    public function update(
        ServerRequestInterface     $request,
        #[RouteArgument('id')] int $id): ResponseInterface
    {
        return $this->edit($request, $id);
    }

    private function edit(
        ServerRequestInterface      $request,
        #[RouteArgument('id')] ?int $id = null): ResponseInterface
    {
        $form = new UserForm(
            $this->userRepository,
            $this->authRepository,
            $this->translator);
        try {
            $userWithRoles = $id !== null ? $this->userRepository->find($id) : $this->userRepository->create();
            if ($this->formHydrator->populateFromPost($form, $request)) {
                $roles = [];
                foreach ($userWithRoles->roles as $name => $isGranted) {
                    $roles[$name] = in_array($name, $form->roles);
                }
                $form->roles = $roles;
                if ($this->formHydrator->validate($form)->isValid() &&
                    $this->validate($id, $userWithRoles, $form)) {
                    $user = $userWithRoles->user;
                    $user->setName($form->name);
                    $user->setEmail($form->email);
                    if ($user->getId() === null ||
                        $user->getPasswordHash() !== $form->password) {
                        $user->setPasswordHash($this->passwordHasher->hash($form->password));
                    }
                    $user->setStatus(UserStatus::from($form->status));
                    if ($user->getId() === null ||
                        $user->getStatus() === UserStatus::Inactive) {
                        $user->setAuthKey($this->authKeyRepository->generate());
                    }

                    $entityChangedResult = $this->userRepository->save(new UserWithRoles($user, $form->roles));
                    $this->eventDispatcher->dispatch(new EntityChanged($user, $entityChangedResult->eventType, $this->user->getId()));

                    return $this->responseFactory->createResponse()
                        ->withStatus(Status::FOUND)
                        ->withHeader('Location', $this->urlGenerator->generate('admin/users'));
                }
            } else {
                if ($this->validate($id, $userWithRoles, $form) &&
                    $userWithRoles->user->getId() !== null) {
                    $form->id = $userWithRoles->user->getId();
                    $form->name = $userWithRoles->user->getName();
                    $form->email = $userWithRoles->user->getEmail();
                    $form->password = $userWithRoles->user->getPasswordHash();
                    $form->status = $userWithRoles->user->getStatus()->value;
                }
                $form->roles = $userWithRoles->roles;
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
        ?int          $id,
        UserWithRoles $userWithRoles,
        UserForm      $form): bool
    {
        if ($id !== null &&
            $userWithRoles->user->getId() === null) {
            $form->addError($this->translator->translate(App::THE_RECORD_WAS_NOT_FOUND));
            return false;
        }
        return true;
    }

    #[Permission(RbacPermission::DELETE_USER)]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $form = new DeleteEntitiesForm();
            if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
                $entityChangedResultList = $this->userRepository->delete($form->delete_ids);
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
