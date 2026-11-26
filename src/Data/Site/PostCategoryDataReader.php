<?php

namespace App\Data\Site;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use App\Data\Entities\Category;
use App\Data\Entities\PostCategory;
use App\Data\Post\PostStatus;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;

/**
 * @template-implements LimitableDataInterface<int, PostCategory>
 * @template-implements OffsetableDataInterface<int, PostCategory>
 */
final class PostCategoryDataReader implements LimitableDataInterface, OffsetableDataInterface, CountableDataInterface
{
    /** @var non-negative-int|null $limit */
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
        /** @var non-negative-int $scalarResult */
        $scalarResult = $this->createPostCategoryQueryBuilder()
            ->addSelect('count(pc.post)')
            ->getQuery()
            ->getSingleScalarResult();

        return $scalarResult;
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
     * @return iterable<int, PostCategory>
     */
    public function read(): iterable
    {
        $qb = $this->createPostCategoryQueryBuilder()
            ->addSelect('pc');
        $qb->setFirstResult($this->offset);
        if ($this->limit !== null) {
            $qb->setMaxResults($this->limit);
        }

        /** @var iterable<int, PostCategory> $result */
        $result = $qb
            ->getQuery()
            ->getResult();

        return $result;
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
     * @return PostCategory|null
     */
    public function readOne(): PostCategory|null
    {
        if ($this->limit > 0) {
            /** @var array<PostCategory> $result */
            $result = $this->createPostCategoryQueryBuilder()
                ->addSelect('pc')
                ->setFirstResult($this->offset)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();

            if (!empty($result)) {
                return $result[0];
            }
        }

        return null;
    }
}
