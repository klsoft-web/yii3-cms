<?php

namespace App\Data\Entities;

use App\Data\Id\IdProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Category implements IdProviderInterface
{
    public const SUMMARY_LENGTH = 768;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'slug', referencedColumnName: 'id')]
    private ?Slug $slug = null;
    #[ORM\Column(type: 'string', length: Slug::ID_LENGTH)]
    private string $name;

    #[ORM\Column(type: 'string', length: self::SUMMARY_LENGTH, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $summary_img_path = null;

    #[ORM\Column(name: 'category_order', type: 'smallint')]
    private int $order;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'parent', referencedColumnName: 'slug', nullable: true)]
    private ?Category $parent = null;

    #[ORM\JoinTable(name: 'categories_meta')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'slug')]
    #[ORM\InverseJoinColumn(name: 'meta_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: Meta::class, fetch: 'EAGER')]
    private Collection $meta_items;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private User $created_by_user;

    public function __construct()
    {
        $this->meta_items = new ArrayCollection();
    }

    /**
     * @return Slug|null
     */
    public function getSlug(): ?Slug
    {
        return $this->slug;
    }

    /**
     * @param Slug $slug
     */
    public function setSlug(Slug $slug): void
    {
        $this->slug = $slug;
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
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string|null $summary
     */
    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return string|null
     */
    public function getSummaryImgPath(): ?string
    {
        return $this->summary_img_path;
    }

    /**
     * @param string|null $summaryImgPath
     */
    public function setSummaryImgPath(?string $summaryImgPath): void
    {
        $this->summary_img_path = $summaryImgPath;
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
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category|null $parent
     */
    public function setParent(?Category $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection
     */
    public function getMetaItems(): Collection
    {
        return $this->meta_items;
    }

    /**
     * @param Collection $metaItems
     */
    public function setMetaItems(Collection $metaItems): void
    {
        $this->meta_items = $metaItems;
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
        return $this->slug->getId();
    }
}
