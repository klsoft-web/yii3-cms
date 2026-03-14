<?php

namespace App\Admin\Data\Category;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\Category;
use App\Data\Entities\Slug;
use Throwable;

interface CategoryAdminRepositoryInterface
{
    /**
     * @throws Throwable
     */
    public function save(Category $category): EntityChangedResult;

    /**
     * @throws Throwable
     */
    public function updateSlug(Category $category, Slug $slug): EntityChangedResult;

    /**
     * @throws Throwable
     */
    public function delete(array $slugs): array;

    /**
     * @throws Throwable
     */
    public function find(Slug $slug): ?Category;

    public function findByName(string $name): ?Category;

    public function getAll(): array;

    public function findAllByNameLikeTo(
        string $text,
        int    $offset,
        ?int   $limit,
        string $sort,
        string $order): array;
}
