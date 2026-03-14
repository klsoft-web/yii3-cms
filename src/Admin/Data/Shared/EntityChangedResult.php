<?php

namespace App\Admin\Data\Shared;

use App\Data\Id\IdProviderInterface;
use App\Data\Log\EntityEventType;

final readonly class EntityChangedResult
{
    public function __construct(public IdProviderInterface $entity, public EntityEventType $eventType)
    {
    }
}
