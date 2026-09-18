<?php

namespace App\Admin\Data\Post;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\Category;
use App\Data\Entities\Meta;
use App\Data\Entities\NavItem;
use App\Data\Entities\Post;
use App\Data\Entities\PostCategory;
use App\Data\Entities\Slug;
use App\Data\Log\EntityEventType;
use App\Data\Post\PostType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PostAdminRepository implements PostAdminRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Post $post, bool $removeMetaItems, array $categories): EntityChangedResult
    {
        $isNewEntity = $this->entityManager->find(Post::class, $post->getSlug()) === null;
        $removedMataItems = [];
        if ($removeMetaItems) {
            $removedMataItems = $post->getMetaItems();
            $post->setMetaItems(new ArrayCollection([]));
        }
        $this->entityManager->persist($post);
        if (!$isNewEntity) {
            $postCategories = $this->getPostCategories($post);
            foreach ($postCategories as $postCategory) {
                $catId = $postCategory->getCategory()->getSlug()?->getId();
                if (in_array($catId, $categories)) {
                    $categories = array_diff($categories, [$catId]);
                } else {
                    $this->entityManager->remove($postCategory);
                }
            }
        }
        foreach ($categories as $categoryId) {
            $postCategory = new PostCategory();
            $postCategory->setPost($post);
            $category = $this->entityManager->find(Category::class, $categoryId);
            if ($category !== null) {
                $postCategory->setCategory($category);
                $this->entityManager->persist($postCategory);
            }
        }
        /** @var Meta $metaItem */
        foreach ($post->getMetaItems() as $metaItem) {
            $this->entityManager->persist($metaItem);
        }
        /** @var Meta $metaItem */
        foreach ($removedMataItems as $metaItem) {
            $this->entityManager->remove($metaItem);
        }
        $this->entityManager->flush();

        return new EntityChangedResult($post, $isNewEntity ? EntityEventType::Insert : EntityEventType::Update);
    }

    public function updateSlug(Post $post, Slug $slug, bool $removeMetaItems, array $categories): EntityChangedResult
    {
        $newPost = new Post();
        $newPost->setSlug($slug);
        $newPost->setPostType($post->getPostType());
        $newPost->setStatus($post->getStatus());
        $newPost->setName($post->getName());
        $newPost->setDateTime($post->getDateTime());
        $newPost->setSummary($post->getSummary());
        $newPost->setSummaryImgPath($post->getSummaryImgPath());
        $newPost->setContent($post->getContent());
        $newPost->setCreatedByUser($post->getCreatedByUser());
        $metaItems = [];
        /** @var array<Meta> $removedMataItems */
        $removedMataItems = $post->getMetaItems();
        foreach ($removedMataItems as $metaItem) {
            $meta = new Meta();
            $meta->setName($metaItem->getName());
            $meta->setContent($metaItem->getContent());
            $metaItems[] = $meta;
        }
        /** @var Collection $collection */
        $collection = new ArrayCollection($removeMetaItems ? [] : $metaItems);
        $newPost->setMetaItems($collection);

        $this->entityManager->wrapInTransaction(function ($em) use ($removedMataItems, $post, $newPost) {
            $em->remove($post);
            /** @var Slug $slug */
            $slug = $post->getSlug();
            $em->remove($slug);
            foreach ($removedMataItems as $metaItem) {
                $em->remove($metaItem);
            }
            /** @var Meta $metaItem */
            foreach ($newPost->getMetaItems() as $metaItem) {
                $em->persist($metaItem);
            }
            $em->persist($newPost);
            $postCategories = $this->getPostCategories($post);
            $categories = [];
            foreach ($postCategories as $postCategory) {
                $categories[] = $postCategory->getCategory();
                $em->remove($postCategory);
            }
            foreach ($categories as $category) {
                $postCategory = new PostCategory();
                $postCategory->setPost($newPost);
                $postCategory->setCategory($category);
                $em->persist($postCategory);
            }
            $em->flush();

            /** @var string $postSlugId */
            $postSlugId = $post->getSlug()?->getId();
            /** @var string $newPostSlugId */
            $newPostSlugId = $newPost->getSlug()?->getId();
            $this->updateNavItems($postSlugId, $newPostSlugId, $em);
        });

        return new EntityChangedResult($newPost, EntityEventType::Insert);
    }

    private function updateNavItems(
        string                 $oldSlug,
        string                 $newSlug,
        EntityManagerInterface $em): void
    {
        $em->createQueryBuilder()
            ->update(NavItem::class, 'n')
            ->set('n.value', ':newSlug')
            ->where('n.value = :oldSlug')
            ->setParameter('oldSlug', $oldSlug)
            ->setParameter('newSlug', $newSlug)
            ->getQuery()
            ->execute();
    }

    public function delete(array $slugs): array
    {
        $deletedEntities = [];
        foreach ($slugs as $slug) {
            $post = $this->entityManager->find(Post::class, $slug);
            if ($post !== null) {
                $postRemoved = clone $post;
                /** @var array<Meta> $removedMataItems */
                $removedMataItems = $post->getMetaItems();
                $this->entityManager->remove($post);
                $deletedEntities[] = new EntityChangedResult($postRemoved, EntityEventType::Delete);
                $this->entityManager->remove($slug);
                $postCategories = $this->getPostCategories($post);
                foreach ($postCategories as $postCategory) {
                    $this->entityManager->remove($postCategory);
                }
                foreach ($removedMataItems as $metaItem) {
                    $this->entityManager->remove($metaItem);
                }
            }
        }
        $this->entityManager->flush();

        return $deletedEntities;
    }

    public function find(Slug $slug): ?Post
    {
        return $this->entityManager->find(Post::class, $slug);
    }

    public function findByName(string $name): ?Post
    {
        /** @var Post|null $post */
        $post = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Post::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        return $post;
    }

    public function findAllPagesByNameLikeTo(
        string $text,
        int    $offset = 0,
        ?int   $limit = null,
        string $sort = 'date_time',
        string $order = 'DESC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Post::class, 'p')
            ->setFirstResult($offset);
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        /** @var array<Post> $result */
        $result = $qb
            ->where($qb->expr()->eq('p.post_type', ':post_type'), $qb->expr()->like('p.name', ':text'))
            ->setParameter('post_type', PostType::Page)
            ->setParameter('text', '%' . $text . '%')
            ->orderBy("p.$sort", $order)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return array<PostCategory>
     */
    public function getPostCategories(Post $post): array
    {
        /** @var array<PostCategory> $result */
        $result = $this->entityManager->createQueryBuilder()
            ->select('pc')
            ->from(PostCategory::class, 'pc')
            ->where('pc.post = :post')
            ->setParameter('post', $post)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
