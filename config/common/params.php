<?php

declare(strict_types=1);

use App\Domain\Site\SiteManagerInterface;
use App\Shared\ApplicationParams;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Definitions\Reference;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\View\Renderer\CsrfViewInjection;

return [
    'application' => require __DIR__ . '/application.php',

    'yiisoft/aliases' => [
        'aliases' => require __DIR__ . '/aliases.php',
    ],

    'yiisoft/view' => [
        'basePath' => null,
        'parameters' => [
            'assetManager' => Reference::to(AssetManager::class),
            'applicationParams' => Reference::to(ApplicationParams::class),
            'aliases' => Reference::to(Aliases::class),
            'urlGenerator' => Reference::to(UrlGeneratorInterface::class),
            'currentRoute' => Reference::to(CurrentRoute::class),
            'gridView' => Reference::to(GridView::class),
            'listView' => Reference::to(ListView::class),
            'translator' => Reference::to(TranslatorInterface::class),
            'siteManager' => Reference::to(SiteManagerInterface::class),
        ],
    ],

    'yiisoft/yii-view-renderer' => [
        'viewPath' => null,
        'layout' => '@src/Web/Shared/Layout/Main/layout.php',
        'injections' => [
            Reference::to(CsrfViewInjection::class),
        ],
    ],

    'doctrine' => [
        'paths' => [
            __DIR__ . '/../../src/Data/Entities'
        ],
        'isDevMode' => false,
        'connection' => [
            'driver' => 'pdo_mysql',
            'user' => 'mysite',
            'password' => 'secret',
            'dbname' => 'mysite'
        ]
    ],

    'auth' => [
        'minPasswordLength' => 6
    ],

    'messagesPath' => __DIR__ . '/../../src/Messages',
    'homePageSlug' => 'homepage',
    'uploadImagesDir' => 'uploads/images',
    'uploadFilesDir' => 'uploads/files',
];
