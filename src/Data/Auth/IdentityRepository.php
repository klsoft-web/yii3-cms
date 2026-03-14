<?php

namespace App\Data\Auth;

use App\Data\User\UserRepositoryInterface;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

final readonly class IdentityRepository implements IdentityRepositoryInterface
{
    public function __construct(
        private UserRepositoryInterface    $userRepository,
        private AuthKeyRepositoryInterface $authKeyRepository)
    {
    }

    public function findIdentity(string $id): ?IdentityInterface
    {
        $user = $this->userRepository->find($id);
        if ($user !== null) {
            return new CookieLoginIdentity(new Identity($user), $user, $this->authKeyRepository);
        }
        return null;
    }
}
