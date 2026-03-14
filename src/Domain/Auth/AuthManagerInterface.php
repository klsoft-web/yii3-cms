<?php

namespace App\Domain\Auth;

use App\Data\Entities\User;

interface AuthManagerInterface
{
    public function validateCredentialsThenFindIdentity(string $name, string $password): AuthResult;

    public function saveUserThenFindIdentity(string $name, string $password, string $email): AuthResult;

    public function refreshAuthKey(User $user): void;
}
