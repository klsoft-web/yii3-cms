<?php

namespace App\Data\User;

use App\Data\Entities\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function delete(User $user): void;

    public function find(int $id): ?User;

    public function findByName(string $name): ?User;

    public function findByNameOrEmail(string $name, string $email): ?User;
}
