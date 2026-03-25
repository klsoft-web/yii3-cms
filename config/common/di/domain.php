<?php

declare(strict_types=1);

use App\Domain\Auth\AuthManager;
use App\Domain\Auth\AuthManagerInterface;
use App\Domain\Site\SiteManager;
use App\Domain\Site\SiteManagerInterface;

return [
    AuthManagerInterface::class => AuthManager::class,
    SiteManagerInterface::class => SiteManager::class,
];
