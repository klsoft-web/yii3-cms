<?php

namespace App\Admin\Data\Nav;

use App\Admin\Data\Shared\EntityChangedResult;
use Klsoft\Yii3CmsCore\Data\Entities\Nav;
use Klsoft\Yii3CmsCore\Data\Entities\NavItem;
use Throwable;

interface NavAdminRepositoryInterface
{
    /**
     * @throws Throwable
     */
    public function save(Nav $nav, array $navItems): EntityChangedResult;

    /**
     * @throws Throwable
     */
    public function delete(array $ids): array;

    /**
     * @throws Throwable
     */
    public function find(int $id): ?Nav;

    public function findByName(string $name): ?Nav;

    /**
     * @throws Throwable
     */
    public function findNavItem(int $id): ?NavItem;
    public function getAllNavItemsByNav(Nav $nav, ?NavItem $parent): array;
}
