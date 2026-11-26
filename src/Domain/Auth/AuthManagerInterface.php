<?php

namespace App\Domain\Auth;

use App\Data\Entities\User;
use Exception;
use Throwable;

interface AuthManagerInterface
{
    public function validateCredentials(string $name, string $password): AuthResult;

    /**
     * @throws Throwable
     */
    public function saveUser(string $name, string $password, string $email): AuthResult;

    /**
     * @throws Exception
     */
    public function refreshAuthKey(User $user): void;
}
