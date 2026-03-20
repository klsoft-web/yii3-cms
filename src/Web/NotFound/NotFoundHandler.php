<?php

declare(strict_types=1);

namespace App\Web\NotFound;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class NotFoundHandler implements RequestHandlerInterface
{
    public function __construct(private WebViewRenderer $viewRenderer)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->viewRenderer
            ->render(__DIR__ . '/template')
            ->withStatus(Status::NOT_FOUND);
    }
}
