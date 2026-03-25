<?php

namespace App\Data\User;

use Klsoft\Yii3CmsCore\Data\Entities\User;
use Throwable;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function delete(User $user): void;

    /**
     * @throws Throwable
     */
    public function find(int $id): ?User;

    public function findByName(string $name): ?User;

    public function findByEmail(string $email): ?User;
}
