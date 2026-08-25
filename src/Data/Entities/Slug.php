<?php

namespace App\Data\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Slug
{
    public const ID_LENGTH = 255;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: self::ID_LENGTH)]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $entity_class;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entity_class;
    }

    /**
     * @param string $entity_class
     */
    public function setEntityClass(string $entity_class): void
    {
        $this->entity_class = $entity_class;
    }
}
