<?php

namespace App\Admin\Data\Role;

interface RoleRepositoryInterface
{
    public function create(): RoleWithPermissions;
    public function save(RoleWithPermissions $roleWithPermissions): void;
    public function delete(array $names): void;
    public function find(string $name): RoleWithPermissions;
    public function getGroupsOfPermissions(): array;
    public function userHasPermission(string $userId, string $permissionName): bool;
}
