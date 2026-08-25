<?php

declare(strict_types=1);

use App\Admin\Domain\Slug\SlugManager;
use App\Admin\Domain\Slug\SlugManagerInterface;
use App\Domain\Auth\AuthManager;
use App\Domain\Auth\AuthManagerInterface;
use App\Domain\Site\SiteManager;
use App\Domain\Site\SiteManagerInterface;

return [
    AuthManagerInterface::class => AuthManager::class,
    SlugManagerInterface::class => SlugManager::class,
    SiteManagerInterface::class => SiteManager::class,
];
