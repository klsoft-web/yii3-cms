<?php

namespace App\Data\Entities;

use App\Data\Nav\NavItemType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity]
class NavItem
{
    public const VALUE_LENGTH = Slug::ID_LENGTH + 1;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: Types::ENUM)]
    private NavItemType $nav_item_type = NavItemType::Slug;

    #[ORM\Column(type: 'string', length: self::VALUE_LENGTH)]
    private string $value;


    #[ORM\Column(name: 'nav_item_order', type: 'smallint')]
    private int $order;

    #[ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Nav $nav;

    #[ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true)]
    private ?NavItem $parent = null;


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
     * @return NavItemType
     */
    public function getNavItemType(): NavItemType
    {
        return $this->nav_item_type;
    }

    /**
     * @param NavItemType $navItemType
     */
    public function setNavItemType(NavItemType $navItemType): void
    {
        $this->nav_item_type = $navItemType;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
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
     * @return Nav
     */
    public function getNav(): Nav
    {
        return $this->nav;
    }

    /**
     * @param Nav $nav
     */
    public function setNav(Nav $nav): void
    {
        $this->nav = $nav;
    }

    /**
     * @return NavItem|null
     */
    public function getParent(): ?NavItem
    {
        return $this->parent;
    }

    /**
     * @param NavItem|null $parent
     */
    public function setParent(?NavItem $parent): void
    {
        $this->parent = $parent;
    }
}
