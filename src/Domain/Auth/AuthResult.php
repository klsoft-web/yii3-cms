<?php

namespace App\Domain\Auth;

use App\Data\Entities\User;
use Yiisoft\Auth\IdentityInterface;

final readonly class AuthResult
{

    /**
     * @param IdentityInterface|null $identity
     * @param User|null $user
     * @param string[] $errors
     */
    public function __construct(
        public ?IdentityInterface $identity,
        public ?User              $user,
        public array              $errors = [])
    {
    }
}
