<?php

namespace App\Admin\Data\Events;

use App\Data\Id\IdProviderInterface;
use App\Data\Log\EntityEventType;

final readonly class EntityChanged
{
    public function __construct(
        public IdProviderInterface $entity,
        public EntityEventType     $eventType,
        public int                 $userId)
    {
    }
}
