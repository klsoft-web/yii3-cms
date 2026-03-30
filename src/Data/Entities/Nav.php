<?php

namespace App\Data\Entities;

use App\Data\Id\IdProviderInterface;
use App\Data\Nav\NavPosition;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Nav implements IdProviderInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $name;

    #[ORM\Column(type: Types::ENUM)]
    private NavPosition $position = NavPosition::Top;

    #[ORM\Column(name: 'nav_order', type: 'smallint')]
    private int $order;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private User $created_by_user;

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
     * @return NavPosition
     */
    public function getPosition(): NavPosition
    {
        return $this->position;
    }

    /**
     * @param NavPosition $position
     */
    public function setPosition(NavPosition $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    /**
     * @return User
     */
    public function getCreatedByUser(): User
    {
        return $this->created_by_user;
    }

    /**
     * @param User $createdByUser
     */
    public function setCreatedByUser(User $createdByUser): void
    {
        $this->created_by_user = $createdByUser;
    }

    public function getIdAsString(): ?string
    {
        return $this->getId();
    }
}
