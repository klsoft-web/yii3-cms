<?php

namespace App\Data\Auth;

use Klsoft\Yii3CmsCore\Data\Entities\User;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\User\Login\Cookie\CookieLoginIdentityInterface;

final readonly class CookieLoginIdentity implements CookieLoginIdentityInterface
{
    public function __construct(
        private IdentityInterface          $identity,
        private User                       $user,
        private AuthKeyRepositoryInterface $authKeyRepository)
    {
    }

    public function getCookieLoginKey(): string
    {
        return $this->user->getAuthKey();
    }

    public function validateCookieLoginKey(string $key): bool
    {
        return $this->authKeyRepository->validate($key, $this->user->getAuthKey());
    }

    public function getId(): ?string
    {
        return $this->identity->getId();
    }
}
