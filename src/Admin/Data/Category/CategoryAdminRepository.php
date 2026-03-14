<?php

namespace App\Admin\Data\Category;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\Category;
use App\Data\Entities\Meta;
use App\Data\Entities\NavItem;
use App\Data\Entities\Post;
use App\Data\Entities\Slug;
use App\Data\Log\EntityEventType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CategoryAdminRepository implements CategoryAdminRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }


    public function save(Category $category, bool $removeMetaItems = false): EntityChangedResult
    {
        $isNewEntity = $this->entityManager->find(Category::class, $category->getSlug()) === null;
        $removedMataItems = [];
        if ($removeMetaItems) {
            $removedMataItems = $category->getMetaItems();
            $category->setMetaItems(new ArrayCollection([]));
        }
        $this->entityManager->persist($category);
        foreach ($category->getMetaItems() as $metaItem) {
            $this->entityManager->persist($metaItem);
        }
        foreach ($removedMataItems as $metaItem) {
            $this->entityManager->remove($metaItem);
        }
        $this->entityManager->flush();

        return new EntityChangedResult($category, $isNewEntity ? EntityEventType::Insert : EntityEventType::Update);
    }

    public function updateSlug(Category $category, Slug $slug): EntityChangedResult
    {
        $newCategory = new Category();
        $newCategory->setSlug($slug);
        $newCategory->setName($category->getName());
        $newCategory->setSummary($category->getSummary());
        $newCategory->setSummaryImgPath($category->getSummaryImgPath());
        $newCategory->setOrder($category->getOrder());
        $newCategory->setCreatedByUser($category->getCreatedByUser());
        $metaItems = [];
        $removedMataItems = $category->getMetaItems();
        foreach ($removedMataItems as $metaItem) {
            $meta = new Meta();
            $meta->setName($metaItem->getName());
            $meta->setContent($metaItem->getContent());
            $metaItems[] = $meta;
        }
        $newCategory->setMetaItems(new ArrayCollection($metaItems));

        $this->entityManager->wrapInTransaction(function ($em) use ($removedMataItems, $category, $newCategory) {
            foreach ($newCategory->getMetaItems() as $metaItem) {
                $em->persist($metaItem);
            }
            $em->persist($newCategory);
            $em->flush();

            $this->updatePosts($category, $newCategory, $em);
            $this->updateNavItems($category->getSlug()->getId(), $newCategory->getSlug()->getId(), $em);

            $em->remove($category);
            $em->remove($category->getSlug());
            foreach ($removedMataItems as $metaItem) {
                $em->remove($metaItem);
            }
            $em->flush();
        });

        return new EntityChangedResult($newCategory, EntityEventType::Insert);
    }

    private function updatePosts(
        Category               $oldCategory,
        Category               $newCategory,
        EntityManagerInterface $em): void
    {
        $em->createQueryBuilder()
            ->update(Post::class, 'p')
            ->set('p.category', ':newCategory')
            ->where('p.category = :oldCategory')
            ->setParameter('oldCategory', $oldCategory)
            ->setParameter('newCategory', $newCategory)
            ->getQuery()
            ->execute();
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
            $category = $this->entityManager->find(Category::class, $slug);
            if ($category !== null) {
                $categoryRemoved = clone $category;
                $removedMataItems = $category->getMetaItems();
                $this->entityManager->remove($category);
                $deletedEntities[] = new EntityChangedResult($categoryRemoved, EntityEventType::Delete);
                foreach ($removedMataItems as $metaItem) {
                    $this->entityManager->remove($metaItem);
                }
                $this->entityManager->remove($slug);
            }
        }
        $this->entityManager->flush();

        return $deletedEntities;
    }

    public function find(Slug $slug): ?Category
    {
        return $this->entityManager->find(Category::class, $slug);
    }

    public function findByName(string $name): ?Category
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->where('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByNameLikeTo(
        string $text,
        int    $offset = 0,
        ?int   $limit = 20,
        string $sort = 'name',
        string $order = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->setFirstResult($offset);
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb
            ->where($qb->expr()->like('c.name', ':text'))
            ->setParameter('text', '%' . $text . '%')
            ->orderBy("c.$sort", $order)
            ->getQuery()
            ->getResult();
    }
}
