<?php

declare(strict_types=1);

use App\Admin\Web\Authz\AdminApiAuthorization;
use App\Admin\Web;
use Klsoft\Yii3Auth\Middleware\Authentication;
use Klsoft\Yii3Authz\Middleware\Authorization;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create()
        ->middleware(Authentication::class)
        ->middleware(Authorization::class)
        ->routes(
            Route::get('/admin/forbidden')
                ->action(Web\Forbidden\Action::class)
                ->name('admin/forbidden'),
            Route::get('/admin/posts')
                ->action([Web\Post\PostController::class, 'list'])
                ->name('admin/posts'),
            Route::methods(['GET', 'POST'], '/admin/post')
                ->action([Web\Post\PostController::class, 'create'])
                ->name('admin/create-post'),
            Route::methods(['GET', 'POST'], '/admin/post/{id}')
                ->action([Web\Post\PostController::class, 'update'])
                ->name('admin/update-post'),
            Route::get('/admin/pages')
                ->action([Web\Page\PageController::class, 'list'])
                ->name('admin/pages'),
            Route::methods(['GET', 'POST'], '/admin/page')
                ->action([Web\Page\PageController::class, 'create'])
                ->name('admin/create-page'),
            Route::methods(['GET', 'POST'], '/admin/page/{id}')
                ->action([Web\Page\PageController::class, 'update'])
                ->name('admin/update-page'),
            Route::get('/admin/categories')
                ->action([Web\Category\CategoryController::class, 'list'])
                ->name('admin/categories'),
            Route::methods(['GET', 'POST'], '/admin/category')
                ->action([Web\Category\CategoryController::class, 'create'])
                ->name('admin/create-category'),
            Route::methods(['GET', 'POST'], '/admin/category/{id}')
                ->action([Web\Category\CategoryController::class, 'update'])
                ->name('admin/update-category'),
            Route::get('/admin/navs')
                ->action([Web\Nav\NavController::class, 'list'])
                ->name('admin/navs'),
            Route::methods(['GET', 'POST'], '/admin/nav')
                ->action([Web\Nav\NavController::class, 'create'])
                ->name('admin/create-nav'),
            Route::methods(['GET', 'POST'], '/admin/nav/{id}')
                ->action([Web\Nav\NavController::class, 'update'])
                ->name('admin/update-nav'),
            Route::post('/admin/nav-find-entities')
                ->action([Web\Nav\NavController::class, 'findEntities'])
                ->name('admin/nav-find-entities'),
            Route::post('/admin/nav-fetch-nav-items')
                ->action([Web\Nav\NavController::class, 'fetchNavItems'])
                ->name('admin/nav-fetch-nav-items'),
            Route::get('/admin/users')
                ->action([Web\User\UserController::class, 'list'])
                ->name('admin/users'),
            Route::methods(['GET', 'POST'], '/admin/user')
                ->action([Web\User\UserController::class, 'create'])
                ->name('admin/create-user'),
            Route::methods(['GET', 'POST'], '/admin/user/{id}')
                ->action([Web\User\UserController::class, 'update'])
                ->name('admin/update-user'),
            Route::get('/admin/roles')
                ->action([Web\Role\RoleController::class, 'list'])
                ->name('admin/roles'),
            Route::methods(['GET', 'POST'], '/admin/role')
                ->action([Web\Role\RoleController::class, 'create'])
                ->name('admin/create-role'),
            Route::methods(['GET', 'POST'], '/admin/role/{id}')
                ->action([Web\Role\RoleController::class, 'update'])
                ->name('admin/update-role'),
            Route::get('/admin/log')
                ->action(Web\Log\Action::class)
                ->name('admin/log'),
            Route::get('/admin/file-browser/browser/{type}')
                ->action([Web\FileBrowser\FileBrowserController::class, 'browser'])
                ->name('admin/file-browser/browser')
        ),

    Group::create()
        ->middleware(Authentication::class)
        ->middleware(AdminApiAuthorization::class)
        ->routes(Route::post('/admin/posts')
            ->action([Web\Post\PostController::class, 'delete'])
            ->name('admin/posts/delete'),
            Route::post('/admin/pages')
                ->action([Web\Page\PageController::class, 'delete'])
                ->name('admin/pages/delete'),
            Route::post('/admin/categories')
                ->action([Web\Category\CategoryController::class, 'delete'])
                ->name('admin/categories/delete'),
            Route::post('/admin/navs')
                ->action([Web\Nav\NavController::class, 'delete'])
                ->name('admin/navs/delete'),
            Route::post('/admin/file-browser/files')
                ->action([Web\FileBrowser\FileBrowserController::class, 'files'])
                ->name('admin/file-browser/files'),
            Route::post('/admin/file-browser/create-folder')
                ->action([Web\FileBrowser\FileBrowserController::class, 'createFolder'])
                ->name('admin/file-browser/create-folder'),
            Route::post('/admin/file-browser/upload')
                ->action([Web\FileBrowser\FileBrowserController::class, 'upload'])
                ->name('admin/file-browser/upload'),
            Route::post('/admin/users')
                ->action([Web\User\UserController::class, 'delete'])
                ->name('admin/users/delete'),
            Route::post('/admin/roles')
                ->action([Web\Role\RoleController::class, 'delete'])
                ->name('admin/roles/delete'),
            Route::post('/admin/slug')
                ->action(Web\Slug\Action::class)
                ->name('admin/slug')
        )
];
