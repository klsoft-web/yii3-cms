<?php

namespace App\Web\Auth;

use App\Data\Auth\AuthRepositoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;

final readonly class AuthenticationFailureHandler implements RequestHandlerInterface
{
    public function __construct(
        private AuthRepositoryInterface  $authRepository,
        private ResponseFactoryInterface $responseFactory,
        private UrlGeneratorInterface    $urlGenerator)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParameters = [$this->authRepository->getRedirectQueryParameterName() => $request->getUri()->__toString()];

        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader('Location', $this->urlGenerator->generate('login', [], $queryParameters));
    }
}
