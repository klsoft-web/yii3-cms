<?php

declare(strict_types=1);

use App\Admin\Web\Authz\AdminApiAuthorization;
use App\Web\NotFound\NotFoundHandler;
use Doctrine\ORM\EntityManagerInterface;
use Klsoft\Yii3Authz\Middleware\Authorization;
use Klsoft\Yii3CacheDoctrine\DoctrineCache;
use Klsoft\Yii3User\Login\Cookie\CookieLogin;
use Psr\Container\ContainerInterface;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Csrf\CsrfTokenMiddleware;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Input\Http\HydratorAttributeParametersResolver;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;
use Yiisoft\RequestProvider\RequestCatcherMiddleware;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Login\Cookie\CookieLoginMiddleware;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\ListView\ListView;
use Yiisoft\Yii\DataView\YiiRouter\UrlCreator;
use Yiisoft\Yii\DataView\YiiRouter\UrlParameterProvider;
use Yiisoft\Yii\Http\Application;

/** @var array $params */

return [
    Application::class => [
        '__construct()' => [
            'dispatcher' => DynamicReference::to([
                'class' => MiddlewareDispatcher::class,
                'withMiddlewares()' => [
                    [
                        ErrorCatcher::class,
                        SessionMiddleware::class,
                        CookieLoginMiddleware::class,
                        CsrfTokenMiddleware::class,
                        RequestCatcherMiddleware::class,
                        Router::class,
                    ],
                ],
            ]),
            'fallbackHandler' => Reference::to(NotFoundHandler::class),
        ],
    ],

    ParametersResolverInterface::class => [
        'class' => CompositeParametersResolver::class,
        '__construct()' => [
            Reference::to(HydratorAttributeParametersResolver::class),
            Reference::to(RequestInputParametersResolver::class),
        ],
    ],

    CurrentUser::class => [
        'withSession()' => [Reference::to(SessionInterface::class)],
        'withAccessChecker()' => [Reference::to(AccessCheckerInterface::class)]
    ],

    CookieLogin::class => [
        '__construct()' => [
            'duration' => $params['yiisoft/user']['cookieLogin']['duration'] !== null ?
                new DateInterval($params['yiisoft/user']['cookieLogin']['duration']) :
                null,
        ],
        'withCookieSecure()' => [false]
    ],

    Authorization::class => [
        'class' => Authorization::class,
        '__construct()' => [
            'forbiddenUrl' => $params['forbiddenUrl']
        ],
    ],
    AdminApiAuthorization::class => [
        'class' => AdminApiAuthorization::class,
        '__construct()' => [
            'forbiddenUrl' => $params['forbiddenUrl']
        ],
    ],

    GridView::class => static function (ContainerInterface $container) use ($params) {
        return GridView::widget()
            ->urlParameterProvider($container->get(UrlParameterProvider::class))
            ->urlCreator($container->get(UrlCreator::class));
    },
    ListView::class => static function (ContainerInterface $container) use ($params) {
        return ListView::widget()
            ->urlParameterProvider($container->get(UrlParameterProvider::class))
            ->urlCreator($container->get(UrlCreator::class));
    },

    CacheInterface::class => static function (ContainerInterface $container) {
        return new Cache(new DoctrineCache($container->get(EntityManagerInterface::class)));
    },
];
