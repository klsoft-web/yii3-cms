<?php

namespace App\Admin\Data\User;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\User;
use Throwable;

interface UserAdminRepositoryInterface
{
    public function create(): UserWithRoles;

    /**
     * @throws Throwable
     */
    public function save(UserWithRoles $userWithRoles): EntityChangedResult;

    /**
     * @param array $ids
     * @return array<EntityChangedResult>
     * @throws Throwable
     */
    public function delete(array $ids): array;

    /**
     * @throws Throwable
     */
    public function find(int $id): UserWithRoles;
    public function findByName(string $name): ?User;
    public function findByEmail(string $email): ?User;
}
