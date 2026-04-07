<?php

namespace App\Data\Entities;

use App\Data\Id\GuidGenerator;
use App\Data\Id\IdProviderInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Meta implements IdProviderInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: GuidGenerator::class)]
    private ?string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $content;

    /**
     * @return String|null
     */
    public function getId(): ?string
    {
        return $this->id;
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

    public function getIdAsString(): ?string
    {
        return $this->getId();
    }
}
