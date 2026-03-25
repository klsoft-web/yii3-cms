<?php

declare(strict_types=1);

namespace App\Web\Main;

use Klsoft\Yii3CmsCore\Data\Entities\Category;
use Klsoft\Yii3CmsCore\Data\Entities\Post;
use App\Domain\Site\SiteManagerInterface;
use App\Web\NotFound\NotFoundHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Status;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private SiteManagerInterface     $siteManager,
        private ResponseFactoryInterface $responseFactory,
        private NotFoundHandler          $notFoundHandler,
        private WebViewRenderer          $viewRenderer)
    {
    }

    public function __invoke(
        ServerRequestInterface               $request,
        #[RouteArgument('slug')] ?string     $slug = null,
        #[RouteArgument('postSlug')] ?string $postSlug = null): ResponseInterface
    {
        if ($postSlug === null) {
            if ($slug === $this->siteManager->getHomePageSlug()) {
                return $this->responseFactory
                    ->createResponse(Status::MOVED_PERMANENTLY)
                    ->withHeader('Location', '/');
            }
            $entity = $this->siteManager->findEntityBySlug($slug);
            if ($entity instanceof Post) {
                return $this->viewRenderer->render(
                    __DIR__ . '/post_template',
                    [
                        'post' => $entity,
                        'category' => null,
                        'isHeaderDisplayed' => $slug !== null
                    ]
                );
            } else if ($entity instanceof Category) {
                return $this->viewRenderer->render(
                    __DIR__ . '/category_template',
                    [
                        'category' => $entity,
                        'dataReader' => $this->siteManager->getDataReaderForCategory($entity)
                    ]
                );
            }
        } else {
            $category = $this->siteManager->findEntityBySlug($slug);
            if ($category instanceof Category) {
                $post = $this->siteManager->findEntityBySlug($postSlug);
                if ($post instanceof Post) {
                    return $this->viewRenderer->render(
                        __DIR__ . '/post_template',
                        [
                            'post' => $post,
                            'category' => $category,
                            'isHeaderDisplayed' => true
                        ]
                    );
                }
            }
        }

        return $this->notFoundHandler->handle($request);
    }
}
