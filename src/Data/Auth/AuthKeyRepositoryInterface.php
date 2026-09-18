<?php

namespace App\Data\Auth;

use Exception;

interface AuthKeyRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function generate(): string;

    /**
     * @throws Exception
     */
    public function validate(string $key, string $originKey): bool;
}
