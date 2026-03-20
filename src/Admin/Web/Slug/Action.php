<?php

declare(strict_types=1);

namespace App\Admin\Web\Slug;

use Klsoft\Yii3Authz\Permission;
use App\Data\Rbac\Permission as RbacPermission;
use Klsoft\Yii3CmsCore\Domain\Slug\SlugManagerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Status;

final readonly class Action
{
    public function __construct(
        private SlugManagerInterface     $slugManager,
        private FormHydrator             $formHydrator,
        private ResponseFactoryInterface $responseFactory)
    {
    }

    #[Permission(RbacPermission::CREATE_POST . '|' . RbacPermission::CREATE_PAGE . '|' . RbacPermission::CREATE_CATEGORY)]
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $form = new SlugForm();
        if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
            $response = $this->responseFactory->createResponse(Status::OK);
            $response->getBody()->write($this->slugManager->create($form->text));
            return $response;
        }
        return $this->responseFactory->createResponse(Status::INTERNAL_SERVER_ERROR);
    }
}
