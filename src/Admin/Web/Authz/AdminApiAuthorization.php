<?php

namespace App\Admin\Web\Authz;

use Klsoft\Yii3Authz\Middleware\Authorization;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\User\CurrentUser;

final class AdminApiAuthorization extends Authorization
{
    public function __construct(
        private readonly string                   $forbiddenUrl,
        private readonly CurrentUser              $currentUser,
        private readonly UrlMatcherInterface      $matcher,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ContainerInterface       $container
    )
    {
        parent::__construct($this->forbiddenUrl, $this->currentUser, $this->matcher, $this->responseFactory, $this->container);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parentResponse = parent::process($request, $handler);

        if ($parentResponse->getStatusCode() === Status::FOUND) {
            return $this->responseFactory->createResponse(Status::FORBIDDEN);
        }
        return $parentResponse;
    }
}
