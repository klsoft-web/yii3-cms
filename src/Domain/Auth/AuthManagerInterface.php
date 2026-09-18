<?php

namespace App\Domain\Auth;

use App\Data\Entities\User;
use Exception;
use Throwable;

interface AuthManagerInterface
{
    public function validateCredentialsThenFindIdentity(string $name, string $password): AuthResult;

    /**
     * @throws Throwable
     */
    public function saveUserThenFindIdentity(string $name, string $password, string $email): AuthResult;

    /**
     * @throws Exception
     */
    public function refreshAuthKey(User $user): void;
}
