<?php

namespace App\Admin\Data\Role;

final readonly class RoleWithPermissions
{
    /**
     * @param ?string $id
     * @param string $name
     * @param array<string, array<string, bool>> $permissions
     */
    public function __construct(
        public ?string $id,
        public string  $name,
        public array   $permissions)
    {
    }
}
