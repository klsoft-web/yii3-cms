<?php

namespace App\Data\Rbac;

interface RbacPermissionsRepositoryInterface
{
    /**
     * @return array Return groups of permissions:
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
