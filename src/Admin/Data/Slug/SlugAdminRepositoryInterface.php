<?php

namespace App\Admin\Data\Slug;

use App\Data\Entities\Slug;
use Throwable;

interface SlugAdminRepositoryInterface
{
    public function save(Slug $slug): void;

    /**
     * @throws Throwable
     */
    public function find(string $id): ?Slug;
}
