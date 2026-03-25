<?php

namespace App\Admin\Data\Slug;

use Klsoft\Yii3CmsCore\Data\Entities\Slug;
use Throwable;

interface SlugAdminRepositoryInterface
{
    public function save(Slug $slug): void;

    /**
     * @throws Throwable
     */
    public function find(string $id): ?Slug;
}
