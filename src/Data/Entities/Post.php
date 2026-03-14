<?php

namespace App\Data\Entities;

use App\Data\Id\IdProviderInterface;
use App\Data\Post\PostStatus;
use App\Data\Post\PostType;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity]
#[ORM\Index(fields: ['name'])]
class Post implements IdProviderInterface
{
    public const SUMMARY_LENGTH = 768;

    #[ORM\Id]
    #[ManyToOne]
    #[ORM\JoinColumn(name: 'slug', referencedColumnName: 'id')]
    private ?Slug $slug = null;

    #[ORM\Column(type: Types::ENUM)]
    private PostType $post_type = PostType::Post;

    #[ORM\Column(type: Types::ENUM)]
    private PostStatus $status = PostStatus::Active;

    #[ORM\Column(type: 'string', length: Slug::ID_LENGTH)]
    private string $name = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date_time;

    #[ORM\Column(type: 'string', length: self::SUMMARY_LENGTH, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $summary_img_path = null;

    #[ORM\Column(type: 'text')]
    private string $content = '';

    #[ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'category', referencedColumnName: 'slug', nullable: true)]
    private ?Category $category = null;

    #[JoinTable(name: 'posts_meta')]
    #[JoinColumn(name: 'post_id', referencedColumnName: 'slug')]
    #[InverseJoinColumn(name: 'meta_id', referencedColumnName: 'id', unique: true)]
    #[ManyToMany(targetEntity: Meta::class, fetch: 'EAGER')]
    private Collection $meta_items;

    #[ManyToOne(fetch: 'EAGER')]
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
     * @return PostType
     */
    public function getPostType(): PostType
    {
        return $this->post_type;
    }

    /**
     * @param PostType $posType
     */
    public function setPostType(PostType $posType): void
    {
        $this->post_type = $posType;
    }

    /**
     * @return PostStatus
     */
    public function getStatus(): PostStatus
    {
        return $this->status;
    }

    /**
     * @param PostStatus $status
     */
    public function setStatus(PostStatus $status): void
    {
        $this->status = $status;
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
     * @return DateTimeImmutable
     */
    public function getDateTime(): DateTimeImmutable
    {
        return $this->date_time;
    }

    /**
     * @param DateTimeImmutable $date_time
     */
    public function setDateTime(DateTimeImmutable $date_time): void
    {
        $this->date_time = $date_time;
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
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
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
        return $this->getSlug()->getId();
    }
}
