<?php

namespace App\Data\Entities;

use App\Data\Id\GuidGenerator;
use App\Data\Log\EntityEventType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity]
class EntityLog
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: GuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(type: Types::ENUM)]
    private EntityEventType $event_type = EntityEventType::Insert;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date_time;

    #[ORM\Column(type: 'string')]
    private string $entity_class;

    #[ORM\Column(type: 'string')]
    private string $entity_id;

    #[ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return EntityEventType
     */
    public function getEventType(): EntityEventType
    {
        return $this->event_type;
    }

    /**
     * @param EntityEventType $eventType
     */
    public function setEventType(EntityEventType $eventType): void
    {
        $this->event_type = $eventType;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateTime(): DateTimeImmutable
    {
        return $this->date_time;
    }

    /**
     * @param DateTimeImmutable $date_time
     */
    public function setDateTime(DateTimeImmutable $date_time): void
    {
        $this->date_time = $date_time;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entity_class;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass(string $entityClass): void
    {
        $this->entity_class = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entity_id;
    }

    /**
     * @param string $entityId
     */
    public function setEntityId(string $entityId): void
    {
        $this->entity_id = $entityId;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
