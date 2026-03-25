<?php

namespace App\Domain\Auth;

use Klsoft\Yii3CmsCore\Data\Entities\User;
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
