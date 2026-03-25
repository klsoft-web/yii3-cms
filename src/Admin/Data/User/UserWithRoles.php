<?php

namespace App\Admin\Data\User;

use Klsoft\Yii3CmsCore\Data\Entities\User;

final readonly class UserWithRoles
{
    public function __construct(
        public User $user,
        public array   $roles)
    {
    }
}
