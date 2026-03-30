<?php

declare(strict_types=1);

namespace App\Admin\Web\Forbidden;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Status;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class Action implements RequestHandlerInterface
{
    public function __construct(private WebViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $this->viewRenderer
            ->withLayout('@src/Admin/Web/Shared/Layout/Main/layout.php');
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->viewRenderer
            ->render(__DIR__ . '/template')
            ->withStatus(Status::NOT_FOUND);
    }
}
