<?php

namespace App\Admin\Data\Role;

use App\Data\Rbac\Permission;
use Klsoft\Yii3CmsCore\Data\Rbac\RbacPermissionsRepositoryInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission as RbacPermission;
use Yiisoft\Rbac\Role;

final readonly class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(
        private ManagerInterface                   $manager,
        private RbacPermissionsRepositoryInterface $rbacPermissionsRepository)
    {
    }

    public function create(): RoleWithPermissions
    {
        $groupsOfPermissions = [];
        foreach ($this->rbacPermissionsRepository->getGroupsOfPermissions() as $group => $permissions) {
            foreach ($permissions as $permission) {
                $groupsOfPermissions[$group][$permission->getName()] = false;
            }
        }
        return new RoleWithPermissions(
            null,
            '',
            $groupsOfPermissions);
    }

    public function save(RoleWithPermissions $roleWithPermissions): void
    {
        if ($roleWithPermissions->id !== null && $roleWithPermissions->id !== $roleWithPermissions->name) {
            $rbacRole = $this->manager->getRole($roleWithPermissions->id);
            if ($rbacRole !== null) {
                $this->manager->updateRole($rbacRole->getName(), new Role($roleWithPermissions->name));
            }
        }
        if ($this->manager->getRole($roleWithPermissions->name) === null) {
            $this->manager->addRole(new Role($roleWithPermissions->name));
        }
        $this->manager->removeChildren($roleWithPermissions->name);

        foreach ($roleWithPermissions->permissions as $permissions) {
            foreach ($permissions as $name => $isGranted) {
                if ($isGranted) {
                    $rbacPermission = $this->manager->getPermission($name);
                    if ($rbacPermission === null) {
                        $rbacPermission = $this->findPermission($name);
                        if ($rbacPermission === null) {
                            continue;
                        }
                        $this->manager->addPermission($rbacPermission);
                        if ($name === Permission::UPDATE_ONLY_YOUR_POSTS) {
                            $this->manager->addChild($name, Permission::UPDATE_POST);
                        } else if ($name === Permission::UPDATE_ONLY_YOUR_PAGES) {
                            $this->manager->addChild($name, Permission::UPDATE_PAGE);
                        } else if ($name === Permission::UPDATE_ONLY_YOUR_CATEGORIES) {
                            $this->manager->addChild($name, Permission::UPDATE_CATEGORY);
                        } else if ($name === Permission::UPDATE_ONLY_YOUR_NAVIGATIONS) {
                            $this->manager->addChild($name, Permission::UPDATE_NAVIGATION);
                        }
                    }
                    $this->manager->addChild($roleWithPermissions->name, $rbacPermission->getName());
                }
            }
        }
    }

    private function findPermission(string $name): ?RbacPermission
    {
        foreach ($this->getGroupsOfPermissions() as $permissions) {
            foreach ($permissions as $permission) {
                if ($permission->getName() === $name) {
                    return $permission;
                }
            }
        }

        return null;
    }

    public function delete(array $names): void
    {
        foreach ($names as $name) {
            $this->manager->removeRole($name);
        }
    }

    public function find(string $name): RoleWithPermissions
    {
        $groupsOfPermissions = [];
        $rbacPermissions = [];
        $role = $this->manager->getRole($name);
        if ($role !== null) {
            $rbacPermissions = array_map(fn($permission) => $permission->getName(), $this->manager->getPermissionsByRoleName($name));
        }
        foreach ($this->rbacPermissionsRepository->getGroupsOfPermissions() as $group => $permissions) {
            foreach ($permissions as $permission) {
                $groupsOfPermissions[$group][$permission->getName()] = in_array($permission->getName(), $rbacPermissions);
            }
        }
        return new RoleWithPermissions(
            $role?->getName() ?? null,
            $role?->getName() ?? '',
            $groupsOfPermissions);
    }

    public function getGroupsOfPermissions(): array
    {
        return $this->rbacPermissionsRepository->getGroupsOfPermissions();
    }

    public function userHasPermission(string $userId, string $permissionName): bool
    {
        $roles = $this->manager->getRolesByUserId($userId);
        foreach ($roles as $role) {
            $permissions = $this->manager->getPermissionsByRoleName($role->getName());
            foreach ($permissions as $permission) {
                if ($permission->getName() === $permissionName) {
                    return true;
                }
            }
        }
        return false;
    }
}
