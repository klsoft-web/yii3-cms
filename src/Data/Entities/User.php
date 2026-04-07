<?php

namespace App\Data\Entities;

use App\Data\Id\IdProviderInterface;
use App\Data\User\UserStatus;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
class User implements IdProviderInterface
{
    public const NAME_LENGTH = 64;
    public const AUTH_KEY_LENGTH = 64;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: Types::ENUM)]
    private UserStatus $status = UserStatus::Active;

    #[ORM\Column(type: 'string')]
    private string $password_hash;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $password_reset_token;

    #[ORM\Column(type: 'string', length: self::AUTH_KEY_LENGTH)]
    private string $auth_key;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return UserStatus
     */
    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    /**
     * @param UserStatus $status
     */
    public function setStatus(UserStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->password_hash = $passwordHash;
    }

    /**
     * @return string|null
     */
    public function getPasswordResetToken(): ?string
    {
        return $this->password_reset_token;
    }

    /**
     * @param string|null $password_reset_token
     */
    public function setPasswordResetToken(?string $password_reset_token): void
    {
        $this->password_reset_token = $password_reset_token;
    }

    /**
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     */
    public function setAuthKey(string $authKey): void
    {
        $this->auth_key = $authKey;
    }

    public function getIdAsString(): ?string
    {
        return $this->getId();
    }
}
