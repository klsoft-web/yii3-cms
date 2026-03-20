<?php

namespace App\Data\Rbac;

use Klsoft\Yii3CmsCore\Data\Rbac\RbacPermissionsRepositoryInterface;
use Yiisoft\Rbac\Permission as RbacPermission;

final readonly class RbacPermissionsRepository implements RbacPermissionsRepositoryInterface
{
    public function getGroupsOfPermissions(): array
    {
        return [
            GroupOfPermissions::POST => [
                new RbacPermission(Permission::CREATE_POST),
                new RbacPermission(Permission::UPDATE_POST),
                (new RbacPermission(Permission::UPDATE_ONLY_YOUR_POSTS))->withRuleName(OnlyYourEntityRule::class),
                new RbacPermission(Permission::DELETE_POST)
            ],
            GroupOfPermissions::PAGE => [
                new RbacPermission(Permission::CREATE_PAGE),
                new RbacPermission(Permission::UPDATE_PAGE),
                (new RbacPermission(Permission::UPDATE_ONLY_YOUR_PAGES))->withRuleName(OnlyYourEntityRule::class),
                new RbacPermission(Permission::DELETE_PAGE)
            ],
            GroupOfPermissions::CATEGORY => [
                new RbacPermission(Permission::CREATE_CATEGORY),
                new RbacPermission(Permission::UPDATE_CATEGORY),
                (new RbacPermission(Permission::UPDATE_ONLY_YOUR_CATEGORIES))->withRuleName(OnlyYourEntityRule::class),
                new RbacPermission(Permission::DELETE_CATEGORY)
            ],
            GroupOfPermissions::NAVIGATION => [
                new RbacPermission(Permission::CREATE_NAVIGATION),
                new RbacPermission(Permission::UPDATE_NAVIGATION),
                (new RbacPermission(Permission::UPDATE_ONLY_YOUR_NAVIGATIONS))->withRuleName(OnlyYourEntityRule::class),
                new RbacPermission(Permission::DELETE_NAVIGATION)
            ],
            GroupOfPermissions::UPLOAD => [
                new RbacPermission(Permission::UPLOAD_IMAGE),
                new RbacPermission(Permission::UPLOAD_FILE)
            ],
            GroupOfPermissions::USER => [
                new RbacPermission(Permission::CREATE_USER),
                new RbacPermission(Permission::UPDATE_USER),
                new RbacPermission(Permission::DELETE_USER)
            ],
            GroupOfPermissions::ROLE => [
                new RbacPermission(Permission::CREATE_ROLE),
                new RbacPermission(Permission::UPDATE_ROLE),
                new RbacPermission(Permission::DELETE_ROLE)
            ],
            GroupOfPermissions::LOG => [
                new RbacPermission(Permission::READ_LOG)
            ]
        ];
    }
}
