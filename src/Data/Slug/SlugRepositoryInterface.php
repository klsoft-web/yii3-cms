<?php

namespace App\Data\Slug;

use App\Data\Entities\Slug;

interface SlugRepositoryInterface
{
    public function find(string $id): ?Slug;
}
