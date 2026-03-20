<?php

declare(strict_types=1);

namespace App\Admin\Web\Log;

use Klsoft\Yii3CmsCore\Data\Entities\EntityLog;
use Doctrine\ORM\EntityManagerInterface;
use Klsoft\Yii3Authz\Permission;
use App\Data\Rbac\Permission as RbacPermission;
use Klsoft\Yii3DataReaderDoctrine\DoctrineDataReader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class Action
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private WebViewRenderer                 $viewRenderer)
    {
        $this->viewRenderer = $this->viewRenderer
            ->withLayout('@src/Admin/Web/Shared/Layout/Main/layout.php');
    }

    #[Permission(RbacPermission::READ_LOG)]
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->viewRenderer
            ->render(
                __DIR__ . '/template',
                [
                    'dataReader' => (new DoctrineDataReader(
                        $this->entityManager,
                        EntityLog::class))
                        ->withSort(Sort::any(['date_time', 'user'])->withOrder(['date_time' => 'desc']))
                ]
            );
    }
}
