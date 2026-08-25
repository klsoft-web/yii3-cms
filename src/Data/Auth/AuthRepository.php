<?php

namespace App\Data\Auth;

final readonly class AuthRepository implements AuthRepositoryInterface
{
    public function __construct(
        private int    $minPasswordLength,
        private string $redirectQueryParameterName)
    {
    }

    public function getMinPasswordLength(): int
    {
        return $this->minPasswordLength;
    }

    public function getRedirectQueryParameterName(): string
    {
        return $this->redirectQueryParameterName;
    }
}
