<?php

declare(strict_types=1);

use App\Admin\Presentation\Authz\AdminApiAuthorization;
use App\Admin\Presentation;
use Klsoft\Yii3Authz\Middleware\Authorization;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create()
        ->middleware(Authentication::class)
        ->middleware(Authorization::class)
        ->routes(
            Route::get('/admin/forbidden')
                ->action(Presentation\Forbidden\Action::class)
                ->name('admin/forbidden'),
            Route::get('/admin/posts')
                ->action([Presentation\Post\PostController::class, 'list'])
                ->name('admin/posts'),
            Route::methods(['GET', 'POST'], '/admin/post')
                ->action([Presentation\Post\PostController::class, 'create'])
                ->name('admin/create-post'),
            Route::methods(['GET', 'POST'], '/admin/post/{id}')
                ->action([Presentation\Post\PostController::class, 'update'])
                ->name('admin/update-post'),
            Route::get('/admin/pages')
                ->action([Presentation\Page\PageController::class, 'list'])
                ->name('admin/pages'),
            Route::methods(['GET', 'POST'], '/admin/page')
                ->action([Presentation\Page\PageController::class, 'create'])
                ->name('admin/create-page'),
            Route::methods(['GET', 'POST'], '/admin/page/{id}')
                ->action([Presentation\Page\PageController::class, 'update'])
                ->name('admin/update-page'),
            Route::get('/admin/categories')
                ->action([Presentation\Category\CategoryController::class, 'list'])
                ->name('admin/categories'),
            Route::methods(['GET', 'POST'], '/admin/category')
                ->action([Presentation\Category\CategoryController::class, 'create'])
                ->name('admin/create-category'),
            Route::methods(['GET', 'POST'], '/admin/category/{id}')
                ->action([Presentation\Category\CategoryController::class, 'update'])
                ->name('admin/update-category'),
            Route::get('/admin/navs')
                ->action([Presentation\Nav\NavController::class, 'list'])
                ->name('admin/navs'),
            Route::methods(['GET', 'POST'], '/admin/nav')
                ->action([Presentation\Nav\NavController::class, 'create'])
                ->name('admin/create-nav'),
            Route::methods(['GET', 'POST'], '/admin/nav/{id}')
                ->action([Presentation\Nav\NavController::class, 'update'])
                ->name('admin/update-nav'),
            Route::post('/admin/nav-find-entities')
                ->action([Presentation\Nav\NavController::class, 'findEntities'])
                ->name('admin/nav-find-entities'),
            Route::post('/admin/nav-fetch-nav-items')
                ->action([Presentation\Nav\NavController::class, 'fetchNavItems'])
                ->name('admin/nav-fetch-nav-items'),
            Route::get('/admin/users')
                ->action([Presentation\User\UserController::class, 'list'])
                ->name('admin/users'),
            Route::methods(['GET', 'POST'], '/admin/user')
                ->action([Presentation\User\UserController::class, 'create'])
                ->name('admin/create-user'),
            Route::methods(['GET', 'POST'], '/admin/user/{id}')
                ->action([Presentation\User\UserController::class, 'update'])
                ->name('admin/update-user'),
            Route::get('/admin/roles')
                ->action([Presentation\Role\RoleController::class, 'list'])
                ->name('admin/roles'),
            Route::methods(['GET', 'POST'], '/admin/role')
                ->action([Presentation\Role\RoleController::class, 'create'])
                ->name('admin/create-role'),
            Route::methods(['GET', 'POST'], '/admin/role/{id}')
                ->action([Presentation\Role\RoleController::class, 'update'])
                ->name('admin/update-role'),
            Route::get('/admin/log')
                ->action(Presentation\Log\Action::class)
                ->name('admin/log'),
            Route::get('/admin/file-browser/browser/{type}')
                ->action([Presentation\FileBrowser\FileBrowserController::class, 'browser'])
                ->name('admin/file-browser/browser'),
        ),

    Group::create()
        ->middleware(Authentication::class)
        ->middleware(AdminApiAuthorization::class)
        ->routes(
            Route::post('/admin/posts')
            ->action([Presentation\Post\PostController::class, 'delete'])
            ->name('admin/posts/delete'),
            Route::post('/admin/pages')
                ->action([Presentation\Page\PageController::class, 'delete'])
                ->name('admin/pages/delete'),
            Route::post('/admin/categories')
                ->action([Presentation\Category\CategoryController::class, 'delete'])
                ->name('admin/categories/delete'),
            Route::post('/admin/navs')
                ->action([Presentation\Nav\NavController::class, 'delete'])
                ->name('admin/navs/delete'),
            Route::post('/admin/file-browser/files')
                ->action([Presentation\FileBrowser\FileBrowserController::class, 'files'])
                ->name('admin/file-browser/files'),
            Route::post('/admin/file-browser/create-folder')
                ->action([Presentation\FileBrowser\FileBrowserController::class, 'createFolder'])
                ->name('admin/file-browser/create-folder'),
            Route::post('/admin/file-browser/upload')
                ->action([Presentation\FileBrowser\FileBrowserController::class, 'upload'])
                ->name('admin/file-browser/upload'),
            Route::post('/admin/users')
                ->action([Presentation\User\UserController::class, 'delete'])
                ->name('admin/users/delete'),
            Route::post('/admin/roles')
                ->action([Presentation\Role\RoleController::class, 'delete'])
                ->name('admin/roles/delete'),
            Route::post('/admin/slug')
                ->action(Presentation\Slug\Action::class)
                ->name('admin/slug'),
        ),
];
