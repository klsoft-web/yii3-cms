<?php

namespace App\Admin\Data\Nav;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\Nav;
use App\Data\Entities\NavItem;
use Throwable;

interface NavAdminRepositoryInterface
{
    /**
     * @param Nav $nav
     * @param array<NavItem> $navItems
     * @return EntityChangedResult
     * @throws Throwable
     */
    public function save(Nav $nav, array $navItems): EntityChangedResult;

    /**
     * @param array $ids
     * @return array<EntityChangedResult>
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

    public function getAllNavItemsByNav(Nav $nav, ?NavItem $parent = null): array;
}
