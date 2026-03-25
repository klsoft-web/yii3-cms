<?php

namespace App\Admin\Data\Role;

final readonly class RoleWithPermissions
{
    public function __construct(
        public ?string $id,
        public string  $name,
        public array   $permissions)
    {
    }
}
