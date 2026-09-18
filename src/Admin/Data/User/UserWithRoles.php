<?php

namespace App\Admin\Data\User;

use App\Data\Entities\User;

final readonly class UserWithRoles
{
    /**
     * @param User $user
     * @param array<string, bool> $roles
     */
    public function __construct(
        public User  $user,
        public array $roles)
    {
    }
}
