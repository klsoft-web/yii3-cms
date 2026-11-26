<?php

namespace App\Data\Rbac;

use App\Data\User\CreatedByUserProviderInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\RuleContext;
use Yiisoft\Rbac\RuleInterface;

final readonly class OnlyYourEntityRule implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function execute(?string $userId, Item $item, RuleContext $context): bool
    {
        /** @var CreatedByUserProviderInterface|null $entity */
        $entity = $context->getParameterValue('entity');
        return $entity !== null && $entity->getCreatedByUser()->getId() == $userId;
    }
}
