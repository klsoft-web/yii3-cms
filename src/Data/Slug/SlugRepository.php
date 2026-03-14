<?php

namespace App\Data\Slug;

use App\Data\Entities\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

final readonly class SlugRepository implements SlugRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function find(string $id): ?Slug
    {
        return $this->entityManager->find(Slug::class, $id);
    }
}
