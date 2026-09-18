<?php

namespace App\Admin\Domain\Slug;

interface SlugManagerInterface
{
    public function create(string $text): string;
}
