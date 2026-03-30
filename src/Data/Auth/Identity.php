<?php

namespace App\Data\Auth;

use App\Data\Entities\User;
use Yiisoft\Auth\IdentityInterface;

final readonly class Identity implements IdentityInterface
{
    public function __construct(private ?User $user)
    {
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        return $this->user?->getId();
    }
}
