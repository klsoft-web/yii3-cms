<?php

namespace App\Data\Auth;

interface AuthRepositoryInterface
{
    public function getMinPasswordLength(): int;

    public function getRedirectQueryParameterName(): string;
}
