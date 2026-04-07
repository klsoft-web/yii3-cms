<?php

namespace App\Data\Id;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Uid\UuidV7;

class GuidGenerator extends AbstractIdGenerator
{
    /**
     * @inheritDoc
     */
    public function generateId(EntityManagerInterface $em, ?object $entity): mixed
    {
        return (new UuidV7())->toString();
    }
}
