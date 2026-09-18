<?php

namespace App\Admin\Data\Role;

use Yiisoft\Rbac\Permission as RbacPermission;

interface RoleRepositoryInterface
{
    public function create(): RoleWithPermissions;

    public function save(RoleWithPermissions $roleWithPermissions): void;

    /**
     * @param array $names
     */
    public function delete(array $names): void;

    public function find(string $name): RoleWithPermissions;

    /**
     * @return array<string, array<RbacPermission>> Return groups of permissions:
     */
    public function getGroupsOfPermissions(): array;

    public function userHasPermission(string $userId, string $permissionName): bool;
}
