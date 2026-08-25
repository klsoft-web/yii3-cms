<?php

namespace App\Admin\Web\Role;

use App\Admin\Data\Role\RoleWithPermissions;
use App\Admin\Data\Role\RoleRepositoryInterface;
use App\Admin\Web\Shared\Form\DeleteEntitiesForm;
use App\Data\Rbac\Permission as RbacPermission;
use App\Messages\App;
use Klsoft\Yii3Authz\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Klsoft\Yii3DataReaderDoctrine\DoctrineDataReader;
use Klsoft\Yii3DataReaderDoctrine\Filter\ObjectEquals;
use Klsoft\Yii3RbacDoctrine\Entities\YiiRbacItem;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Status;
use Yiisoft\Rbac\Item;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class RoleController
{
    public function __construct(
        private readonly EntityManagerInterface   $entityManager,
        private readonly RoleRepositoryInterface  $roleRepository,
        private readonly CurrentUser              $user,
        private readonly FormHydrator             $formHydrator,
        private readonly TranslatorInterface      $translator,
        private readonly UrlGeneratorInterface    $urlGenerator,
        private readonly ResponseFactoryInterface $responseFactory,
        private WebViewRenderer                   $viewRenderer)
    {
        $this->viewRenderer = $this->viewRenderer
            ->withLayout('@src/Admin/Web/Shared/Layout/Main/layout.php');
    }

    #[Permission(RbacPermission::CREATE_ROLE . '|' . RbacPermission::UPDATE_ROLE . '|' . RbacPermission::DELETE_ROLE)]
    public function list(): ResponseInterface
    {
        return $this->viewRenderer
            ->render(
                __DIR__ . '/list_template',
                [
                    'dataReader' => (new DoctrineDataReader(
                        $this->entityManager,
                        YiiRbacItem::class,
                        ['name']))
                        ->withFilter(new ObjectEquals('type', Item::TYPE_ROLE))
                        ->withSort(Sort::any(['name'])->withOrder(['name' => 'asc'])),
                    'canUserCreateRole' => $this->user->can(RbacPermission::CREATE_ROLE),
                    'canUserUpdateRole' => $this->user->can(RbacPermission::UPDATE_ROLE),
                    'canUserDeleteRole' => $this->user->can(RbacPermission::DELETE_ROLE)
                ]
            );
    }

    #[Permission(RbacPermission::CREATE_ROLE)]
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        return $this->edit($request);
    }

    #[Permission(RbacPermission::UPDATE_ROLE)]
    public function update(
        ServerRequestInterface        $request,
        #[RouteArgument('id')] string $id): ResponseInterface
    {
        return $this->edit($request, $id);
    }

    private function edit(
        ServerRequestInterface         $request,
        #[RouteArgument('id')] ?string $id = null): ResponseInterface
    {
        $form = new RoleForm(
            $this->roleRepository,
            $this->translator);
        $roleWithPermissions = $id !== null ? $this->roleRepository->find($id) : $this->roleRepository->create();
        if ($this->formHydrator->populateFromPost($form, $request)) {
            $groupsOfPermissions = [];
            foreach ($this->roleRepository->getGroupsOfPermissions() as $group => $permissions) {
                foreach ($permissions as $permission) {
                    $groupsOfPermissions[$group][$permission->getName()] = in_array($permission->getName(), $form->permissions);
                }
            }
            $form->permissions = $groupsOfPermissions;
            if ($this->formHydrator->validate($form)->isValid() &&
                $this->validate($id, $roleWithPermissions, $form)) {
                $this->roleRepository->save(new RoleWithPermissions(
                    $form->id,
                    $form->name,
                    $form->permissions));

                return $this->responseFactory->createResponse()
                    ->withStatus(Status::FOUND)
                    ->withHeader('Location', $this->urlGenerator->generate('admin/roles'));
            }
        } else {
            if ($this->validate($id, $roleWithPermissions, $form)) {
                $form->id = $roleWithPermissions->id;
                $form->name = $roleWithPermissions->name;
                $form->permissions = $roleWithPermissions->permissions;
            }
        }

        return $this->viewRenderer->render(
            __DIR__ . '/edit_template',
            [
                'form' => $form
            ]
        );
    }

    private function validate(
        ?string             $id,
        RoleWithPermissions $roleWithPermissions,
        RoleForm            $form): bool
    {
        if ($id !== null &&
            $roleWithPermissions->id === null) {
            $form->addError($this->translator->translate(App::THE_RECORD_WAS_NOT_FOUND));
            return false;
        }
        return true;
    }

    #[Permission(RbacPermission::DELETE_ROLE)]
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $form = new DeleteEntitiesForm();
        if ($this->formHydrator->populateFromPost($form, $request)) {
            $this->roleRepository->delete($form->delete_ids);
        }
        return $this->responseFactory->createResponse()->withStatus(Status::OK);
    }
}
