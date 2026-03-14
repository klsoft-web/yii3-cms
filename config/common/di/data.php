<?php

declare(strict_types=1);

use App\Admin\Data\Category\CategoryAdminRepository;
use App\Admin\Data\Category\CategoryAdminRepositoryInterface;
use App\Admin\Data\FileBrowser\FileBrowserRepository;
use App\Admin\Data\FileBrowser\FileBrowserRepositoryInterface;
use App\Admin\Data\Nav\NavAdminRepository;
use App\Admin\Data\Nav\NavAdminRepositoryInterface;
use App\Admin\Data\Post\PostAdminRepository;
use App\Admin\Data\Post\PostAdminRepositoryInterface;
use App\Admin\Data\Role\RoleRepository;
use App\Admin\Data\Role\RoleRepositoryInterface;
use App\Admin\Data\Slug\SlugAdminRepository;
use App\Admin\Data\Slug\SlugAdminRepositoryInterface;
use App\Admin\Data\User\UserAdminRepository;
use App\Admin\Data\User\UserAdminRepositoryInterface;
use App\Data\Auth\AuthKeyRepository;
use App\Data\Auth\AuthKeyRepositoryInterface;
use App\Data\Auth\AuthRepository;
use App\Data\Auth\AuthRepositoryInterface;
use App\Data\Auth\IdentityRepository;
use App\Data\Rbac\RbacPermissionsRepository;
use App\Data\Rbac\RbacPermissionsRepositoryInterface;
use App\Data\Site\SiteRepository;
use App\Data\Site\SiteRepositoryInterface;
use App\Data\Slug\SlugRepository;
use App\Data\Slug\SlugRepositoryInterface;
use App\Data\User\UserRepository;
use App\Data\User\UserRepositoryInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

/** @var array $params */

return [
    UserRepositoryInterface::class => UserRepository::class,
    AuthRepositoryInterface::class => [
        'class' => AuthRepository::class,
        '__construct()' => [
            'minPasswordLength' => $params['auth']['minPasswordLength'],
            'redirectQueryParameterName' => $params['yiisoft/user']['redirectQueryParameterName']
        ]
    ],
    AuthKeyRepositoryInterface::class => [
        'class' => AuthKeyRepository::class,
        '__construct()' => [
            'duration' => $params['yiisoft/user']['cookieLogin']['duration'] !== null ?
                new DateInterval($params['yiisoft/user']['cookieLogin']['duration']) :
                null
        ]
    ],
    IdentityRepositoryInterface::class => IdentityRepository::class,
    RbacPermissionsRepositoryInterface::class => RbacPermissionsRepository::class,
    RoleRepositoryInterface::class => RoleRepository::class,
    UserAdminRepositoryInterface::class => UserAdminRepository::class,
    SlugRepositoryInterface::class => SlugRepository::class,
    SlugAdminRepositoryInterface:: class => SlugAdminRepository::class,
    CategoryAdminRepositoryInterface::class => CategoryAdminRepository::class,
    PostAdminRepositoryInterface::class => PostAdminRepository::class,
    NavAdminRepositoryInterface::class => NavAdminRepository::class,
    FileBrowserRepositoryInterface::class => [
        'class' => FileBrowserRepository::class,
        '__construct()' => [
            'uploadImagesDir' => $params['uploadImagesDir'],
            'uploadFilesDir' => $params['uploadFilesDir']
        ]
    ],
    SiteRepositoryInterface::class => [
        'class' => SiteRepository::class,
        '__construct()' => [
            'homePageSlug' => $params['homePageSlug']
        ]
    ]
];
