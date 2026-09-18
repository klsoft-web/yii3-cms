<?php

declare(strict_types=1);

use App\Admin\Data\Events\EntityChanged;
use App\Domain\Site\SiteManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Data\Entities\EntityLog;
use App\Data\Entities\User;

return [
    EntityChanged::class => [static function (
        EntityChanged          $event,
        EntityManagerInterface $entityManager,
        SiteManagerInterface   $siteManager) {
        $siteManager->entityChanged($event->entity, $event->eventType);

        $user = $entityManager->find(User::class, $event->userId);
        if ($user !== null) {
            try {
                $log = new EntityLog();
                $log->setEventType($event->eventType);
                $log->setDateTime(new DateTimeImmutable());
                $log->setEntityClass($event->entity::class);
                $log->setEntityId($event->entity->getIdAsString());
                $log->setUser($user);
                $entityManager->persist($log);
                $entityManager->flush();
            }
            catch (\Exception $e) {
                $i =$e;
            }
        }
    }]
];
