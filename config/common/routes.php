<?php

declare(strict_types=1);

use App\Web;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create()
        ->routes(
            Route::get('/[{slug}]')
                ->action(Web\Main\Action::class)
                ->name('main'),
            Route::methods(['GET', 'POST'], '/auth/login')
                ->action([Web\Auth\AuthController::class, 'login'])
                ->name('login'),
            Route::post('/auth/logout')
                ->action([Web\Auth\AuthController::class, 'logout'])
                ->name('logout'),
        ),
];
