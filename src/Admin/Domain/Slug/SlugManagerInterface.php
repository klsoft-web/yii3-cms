<?php

namespace App\Admin\Domain\Slug;

use App\Data\Entities\Slug;

interface SlugManagerInterface
{
    public function create(string $text): string;
}
