<?php

declare(strict_types=1);

use App\Presentation;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create()
        ->routes(
            Route::get('/[{slug}]')
                ->action(Presentation\Main\Action::class)
                ->name('main'),
            Route::get('/{slug:(?!admin|auth).*}/{postSlug}')
                ->action(Presentation\Main\Action::class)
                ->name('main-post-category'),
            Route::methods(['GET', 'POST'], '/auth/login')
                ->action([Presentation\Auth\AuthController::class, 'login'])
                ->name('login'),
            Route::post('/auth/logout')
                ->action([Presentation\Auth\AuthController::class, 'logout'])
                ->name('logout'),
        ),
];
