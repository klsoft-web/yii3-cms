<?php

namespace App\Domain\Auth;

use App\Data\Entities\User;
use Yiisoft\Auth\IdentityInterface;

final readonly class AuthResult
{
    public function __construct(
        public ?IdentityInterface $identity,
        public ?User              $user,
        public array              $errors = [])
    {
    }
}
