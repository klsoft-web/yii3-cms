<?php

namespace App\Admin\Data\Nav;

use App\Admin\Data\Shared\EntityChangedResult;
use Klsoft\Yii3CmsCore\Data\Entities\Nav;
use Klsoft\Yii3CmsCore\Data\Entities\NavItem;
use Klsoft\Yii3CmsCore\Data\Log\EntityEventType;
use Doctrine\ORM\EntityManagerInterface;

final readonly class NavAdminRepository implements NavAdminRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Nav $nav, array $navItems): EntityChangedResult
    {
        $isNewEntity = $nav->getId() === null;
        $navItemIds = [];
        $this->entityManager->persist($nav);
        foreach ($navItems as $navItem) {
            $navItem->setNav($nav);
            if ($navItem->getId() !== null) {
                $navItemIds[] = $navItem->getId();
            }
            $this->entityManager->persist($navItem);
        }
        if (!$isNewEntity) {
            $qb = $this->entityManager->createQueryBuilder()
                ->delete(NavItem::class, 'n')
                ->where('n.nav = :nav')
                ->setParameter('nav', $nav);

            if (count($navItemIds) > 0) {
                $qb = $qb
                    ->andWhere($qb->expr()->notIn('n.id', ':navItemIds'))
                    ->setParameter('navItemIds', $navItemIds);
            }
            $qb->getQuery()->execute();
        }
        $this->entityManager->flush();

        return new EntityChangedResult($nav, $isNewEntity ? EntityEventType::Insert : EntityEventType::Update);
    }

    public function delete(array $ids): array
    {
        $deletedEntities = [];
        foreach ($ids as $id) {
            $nav = $this->entityManager->find(Nav::class, $id);
            if ($nav !== null) {
                $navRemoved = clone $nav;
                $this->entityManager->createQueryBuilder()
                    ->delete(NavItem::class, 'n')
                    ->where('n.nav = :nav')
                    ->setParameter('nav', $nav)
                    ->getQuery()
                    ->execute();
                $this->entityManager->remove($nav);
                $deletedEntities[] = new EntityChangedResult($navRemoved, EntityEventType::Delete);
            }
        }
        $this->entityManager->flush();

        return $deletedEntities;
    }

    public function find(int $id): ?Nav
    {
        return $this->entityManager->find(Nav::class, $id);
    }

    public function findByName(string $name): ?Nav
    {
        return $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from(Nav::class, 'n')
            ->where('n.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNavItem(int $id): ?NavItem
    {
        return $this->entityManager->find(NavItem::class, $id);
    }

    public function getAllNavItemsByNav(Nav $nav, ?NavItem $parent = null): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from(NavItem::class, 'n')
            ->where('n.nav = :nav');
        if ($parent !== null) {
            $qb->andWhere('n.parent = :parent');
            $qb->setParameter('parent', $parent);
        } else {
            $qb->andWhere($qb->expr()->isNull('n.parent'));
        }
        return $qb
            ->setParameter('nav', $nav)
            ->orderBy('n.order', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
