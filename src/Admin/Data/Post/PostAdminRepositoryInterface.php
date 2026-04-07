<?php

namespace App\Admin\Data\Post;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\Post;
use App\Data\Entities\Slug;
use Throwable;

interface PostAdminRepositoryInterface
{
    /**
     * @throws Throwable
     */
    public function save(Post $post, bool $removeMetaItems, array $categories): EntityChangedResult;

    /**
     * @throws Throwable
     */
    public function updateSlug(Post $post, Slug $slug, bool $removeMetaItems, array $categories): EntityChangedResult;

    /**
     * @throws Throwable
     */
    public function delete(array $slugs): array;

    /**
     * @throws Throwable
     */
    public function find(Slug $slug): ?Post;

    public function findByName(string $name): ?Post;

    public function findAllPagesByNameLikeTo(
        string $text,
        int    $offset,
        ?int   $limit,
        string $sort,
        string $order): array;

    public function getPostCategories(Post $post): array;
}
