<?php

namespace App\Admin\Data\Post;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\Meta;
use App\Data\Entities\NavItem;
use App\Data\Entities\Post;
use App\Data\Entities\Slug;
use App\Data\Log\EntityEventType;
use App\Data\Post\PostType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class PostAdminRepository implements PostAdminRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Post $post, bool $removeMetaItems = false): EntityChangedResult
    {
        $isNewEntity = $this->entityManager->find(Post::class, $post->getSlug()) === null;
        $removedMataItems = [];
        if ($removeMetaItems) {
            $removedMataItems = $post->getMetaItems();
            $post->setMetaItems(new ArrayCollection([]));
        }
        $this->entityManager->persist($post);
        foreach ($post->getMetaItems() as $metaItem) {
            $this->entityManager->persist($metaItem);
        }
        foreach ($removedMataItems as $metaItem) {
            $this->entityManager->remove($metaItem);
        }
        $this->entityManager->flush();

        return new EntityChangedResult($post, $isNewEntity ? EntityEventType::Insert : EntityEventType::Update);
    }

    public function updateSlug(Post $post, Slug $slug): EntityChangedResult
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
        $newPost->setCategory($post->getCategory());
        $newPost->setCreatedByUser($post->getCreatedByUser());
        $metaItems = [];
        $removedMataItems = $post->getMetaItems();
        foreach ($removedMataItems as $metaItem) {
            $meta = new Meta();
            $meta->setName($metaItem->getName());
            $meta->setContent($metaItem->getContent());
            $metaItems[] = $meta;
        }
        $newPost->setMetaItems(new ArrayCollection($metaItems));

        $this->entityManager->wrapInTransaction(function ($em) use ($removedMataItems, $post, $newPost) {
            $em->remove($post);
            $em->remove($post->getSlug());
            foreach ($removedMataItems as $metaItem) {
                $em->remove($metaItem);
            }

            foreach ($newPost->getMetaItems() as $metaItem) {
                $em->persist($metaItem);
            }
            $em->persist($newPost);
            $em->flush();

            $this->updateNavItems($post->getSlug()->getId(), $newPost->getSlug()->getId(), $em);
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
                $removedMataItems = $post->getMetaItems();
                $this->entityManager->remove($post);
                $deletedEntities[] = new EntityChangedResult($postRemoved, EntityEventType::Delete);
                foreach ($removedMataItems as $metaItem) {
                    $this->entityManager->remove($metaItem);
                }
                $this->entityManager->remove($slug);
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
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Post::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
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

        return $qb
            ->where($qb->expr()->eq('p.post_type', ':post_type'), $qb->expr()->like('p.name', ':text'))
            ->setParameter('post_type', PostType::Page)
            ->setParameter('text', '%' . $text . '%')
            ->orderBy("p.$sort", $order)
            ->getQuery()
            ->getResult();
    }

    public function getAllPages(
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

        return $qb
            ->where($qb->expr()->eq('p.post_type', ':post_type'))
            ->setParameter('post_type', PostType::Page)
            ->orderBy("p.$sort", $order)
            ->getQuery()
            ->getResult();
    }
}
