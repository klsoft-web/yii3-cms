<?php

declare(strict_types=1);

use App\Admin\Web\Authz\AdminApiAuthorization;
use App\Data\Auth\AuthRepositoryInterface;
use App\Web\Auth\AuthenticationFailureHandler;
use App\Web\Auth\CookieLogin;
use App\Web\NotFound\NotFoundHandler;
use Klsoft\Yii3Authz\Middleware\Authorization;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\Access\AccessCheckerInterface;
use Yiisoft\Auth\AuthenticationMethodInterface;
use Yiisoft\Auth\Middleware\Authentication;
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
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Login\Cookie\CookieLoginMiddleware;
use Yiisoft\User\Method\ApiAuth;
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

    AuthenticationMethodInterface::class => ApiAuth::class,
    
    Authentication::class => [
        '__construct()' => [
            'authenticationMethod' => Reference::to(AuthenticationMethodInterface::class),
            'responseFactory' => Reference::to(ResponseFactoryInterface::class),
            'authenticationFailureHandler' => Reference::to(AuthenticationFailureHandler::class),
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
            ->urlCreator($container->get(UrlCreator::class))
            ->summaryTemplate('');
    },
];
