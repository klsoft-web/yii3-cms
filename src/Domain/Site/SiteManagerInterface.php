<?php

namespace App\Domain\Site;

use App\Data\Entities\Category;
use App\Data\Log\EntityEventType;
use Yiisoft\Data\Reader\ReadableDataInterface;

interface SiteManagerInterface
{
    public function getHomePageSlug(): string;
    public function findEntityBySlug(?string $slug): ?object;
    public function getTopNavItems(): array;
    public function getBottomNavs(): array;
    public function getDataReaderForCategory(Category $category): ReadableDataInterface;
    public function entityChanged(object $entity, EntityEventType $eventType): void;
}
