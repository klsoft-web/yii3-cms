<?php

namespace App\Admin\Data\User;

use App\Data\Entities\User;

final readonly class UserWithRoles
{
    public function __construct(
        public User $user,
        public array   $roles)
    {
    }
}
