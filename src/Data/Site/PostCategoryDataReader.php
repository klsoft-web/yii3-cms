<?php

namespace App\Data\Site;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Klsoft\Yii3CmsCore\Data\Entities\Category;
use Klsoft\Yii3CmsCore\Data\Entities\PostCategory;
use Klsoft\Yii3CmsCore\Data\Post\PostStatus;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;

final class PostCategoryDataReader implements LimitableDataInterface, OffsetableDataInterface, CountableDataInterface
{
    private ?int $limit = null;
    private int $offset = 0;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Category               $category)
    {
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->createPostCategoryQueryBuilder()
            ->addSelect('count(pc.post)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    /**
     * @inheritDoc
     */
    public function withLimit(?int $limit): static
    {
        $new = clone $this;
        $new->limit = $limit;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function withOffset(int $offset): static
    {
        $new = clone $this;
        $new->offset = $offset;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    public function read(): iterable
    {
        $qb = $this->createPostCategoryQueryBuilder()
            ->addSelect('pc');
        $qb->setFirstResult($this->offset);
        if ($this->limit !== null) {
            $qb->setMaxResults($this->limit);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    private function createPostCategoryQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(PostCategory::class, 'pc')
            ->join('pc.post', 'p')
            ->where('pc.category = :category', 'p.status = :status')
            ->setParameter('category', $this->category)
            ->setParameter('status', PostStatus::Active)
            ->orderBy('p.date_time', 'DESC');
    }

    /**
     * @inheritDoc
     */
    public function readOne(): array|object|null
    {
        if ($this->limit === 0) {
            return null;
        }

        return $this->createPostCategoryQueryBuilder()
            ->addSelect('pc')
            ->setFirstResult($this->offset)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
