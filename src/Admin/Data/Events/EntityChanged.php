<?php

namespace App\Admin\Data\Events;

use Klsoft\Yii3CmsCore\Data\Id\IdProviderInterface;
use Klsoft\Yii3CmsCore\Data\Log\EntityEventType;

final readonly class EntityChanged
{
    public function __construct(
        public IdProviderInterface $entity,
        public EntityEventType     $eventType,
        public int                 $userId)
    {
    }
}
