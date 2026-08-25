<?php

namespace App\Data\Rbac;

use Yiisoft\Rbac\Permission as RbacPermission;

interface RbacPermissionsRepositoryInterface
{
    /**
     * @return array<string, array<RbacPermission>> Return groups of permissions:
     *
     * ```php
     * use Yiisoft\Rbac\Permission as RbacPermission;
     *
     * return  [
     *     'Post' => [
     *         new RbacPermission('Create post')
     *         new RbacPermission('Update post')
     *     ],
     * ];
     * ```
     */
    public function getGroupsOfPermissions(): array;
}
