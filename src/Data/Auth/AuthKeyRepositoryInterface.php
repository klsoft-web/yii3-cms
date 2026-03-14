<?php

namespace App\Data\Auth;

interface AuthKeyRepositoryInterface
{
    public function generate(): string;

    public function validate(string $key, string $originKey): bool;
}
