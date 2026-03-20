<?php

namespace App\Admin\Data\Shared;

use Klsoft\Yii3CmsCore\Data\Id\IdProviderInterface;
use Klsoft\Yii3CmsCore\Data\Log\EntityEventType;

final readonly class EntityChangedResult
{
    public function __construct(public IdProviderInterface $entity, public EntityEventType $eventType)
    {
    }
}
