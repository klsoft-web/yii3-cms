<?php

namespace App\Admin\Data\Slug;

use App\Data\Entities\Slug;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SlugAdminRepository implements SlugAdminRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Slug $slug): void
    {
        $this->entityManager->persist($slug);
        $this->entityManager->flush();
    }


    public function find(string $id): ?Slug
    {
        return $this->entityManager->find(Slug::class, $id);
    }
}
