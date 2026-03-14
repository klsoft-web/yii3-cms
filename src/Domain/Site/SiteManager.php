<?php

namespace App\Domain\Site;

use App\Data\Entities\Category;
use App\Data\Entities\Nav;
use App\Data\Entities\NavItem;
use App\Data\Entities\Post;
use App\Data\Entities\Slug;
use App\Data\Log\EntityEventType;
use App\Data\Nav\NavPosition;
use App\Data\Post\PostStatus;
use App\Data\Post\PostType;
use App\Data\Site\SiteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Klsoft\Yii3DataReaderDoctrine\DoctrineDataReader;
use Klsoft\Yii3DataReaderDoctrine\Filter\ObjectEquals;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Data\Reader\Filter\AndX;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Sort;

final readonly class SiteManager implements SiteManagerInterface
{
    private const TOP_NAV = 'top_nav';
    private const BOTTOM_NAV = 'bottom_nav';
    private const HOME_PAGE = 'home_page';

    public function __construct(
        private EntityManagerInterface  $entityManager,
        private SiteRepositoryInterface $siteRepository,
        private CacheInterface          $cache)
    {
    }

    public function getHomePageSlug(): string
    {
        return $this->siteRepository->getHomePageSlug();
    }

    public function findEntityBySlug(?string $slug): ?object
    {
        if ($slug === null) {
            return $this->cache->getOrSet(
                self::HOME_PAGE,
                function () {
                    $post = null;
                    $slugEntity = $this->entityManager->find(Slug::class, $this->getHomePageSlug());
                    if ($slugEntity !== null) {
                        $post = $this->entityManager->find(Post::class, $slugEntity);
                    }
                    return $post !== null && $post->getStatus() === PostStatus::Active ? $post : new Post();
                });
        }
        try {
            $slugEntity = $this->entityManager->find(Slug::class, $slug);
            if ($slugEntity !== null) {
                switch ($slugEntity->getEntityClass()) {
                    case Post::class:
                        $post = $this->entityManager->find(Post::class, $slugEntity);
                        if ($post !== null) {
                            return $post->getStatus() === PostStatus::Active ? $post : new Post();
                        }
                        break;
                    case Category::class:
                        return $this->entityManager->find(Category::class, $slugEntity);
                }
            }
        } catch (OptimisticLockException|ORMException) {
            return null;
        }

        return null;
    }

    public function getTopNavItems(): array
    {
        return $this->cache->getOrSet(
            self::TOP_NAV,
            function () {
                $navs = $this->entityManager->createQueryBuilder()->select('n')
                    ->from(Nav::class, 'n')
                    ->where('n.position = :position')
                    ->setParameter('position', NavPosition::Top)
                    ->orderBy('n.order', 'ASC')
                    ->getQuery()
                    ->getResult();
                if (count($navs) > 0) {
                    return $this->entityManager->createQueryBuilder()->select('n')
                        ->from(NavItem::class, 'n')
                        ->where('n.nav = :nav')
                        ->setParameter('nav', $navs[0])
                        ->orderBy('n.order', 'ASC')
                        ->getQuery()
                        ->getArrayResult();
                }
                return [];
            });
    }

    public function getBottomNavs(): array
    {
        return $this->cache->getOrSet(
            self::BOTTOM_NAV,
            function () {
                $bottomNavs = [];
                $navs = $this->entityManager->createQueryBuilder()->select('n')
                    ->from(Nav::class, 'n')
                    ->where('n.position = :position')
                    ->setParameter('position', NavPosition::Bottom)
                    ->orderBy('n.order', 'ASC')
                    ->getQuery()
                    ->getResult();
                foreach ($navs as $nav) {
                    $bottomNavs[$nav->getName()] = $this->entityManager->createQueryBuilder()->select('n')
                        ->from(NavItem::class, 'n')
                        ->where('n.nav = :nav')
                        ->setParameter('nav', $nav)
                        ->orderBy('n.order', 'ASC')
                        ->getQuery()
                        ->getArrayResult();
                }
                return $bottomNavs;
            });
    }

    public function getDataReaderForCategory(Category $category): DoctrineDataReader
    {
        return (new DoctrineDataReader(
            $this->entityManager,
            Post::class))
            ->withFilter(new AndX(
                new Equals('post_type', PostType::Post->value),
                new Equals('status', PostStatus::Active->value),
                new ObjectEquals('category', $category)
            ))
            ->withSort(Sort::any()->withOrder(['date_time' => 'desc']));
    }

    public function entityChanged(object $entity, EntityEventType $eventType): void
    {
        if ($entity instanceof Post) {
            if ($entity->getSlug()->getId() === $this->getHomePageSlug()) {
                $this->cache->remove(self::HOME_PAGE);
            } else if ($eventType === EntityEventType::Insert) {
                $this->findNavItemBySlugThenRemoveNavCache($entity->getSlug()->getId());
            }
        } else if ($entity instanceof Category && $eventType === EntityEventType::Insert) {
            $this->findNavItemBySlugThenRemoveNavCache($entity->getSlug()->getId());
        } else if ($entity instanceof NaV) {
            if ($entity->getPosition() === NavPosition::Top) {
                $this->cache->remove(self::TOP_NAV);
            } elseif ($entity->getPosition() === NavPosition::Bottom) {
                $this->cache->remove(self::BOTTOM_NAV);
            }
        }
    }

    private function findNavItemBySlugThenRemoveNavCache(string $slug): void
    {
        $navItems = $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from(NavItem::class, 'n')
            ->where('n.value = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
        foreach ($navItems as $navItem) {
            if ($navItem->getNav()->getPosition() === NavPosition::Top) {
                $this->cache->remove(self::TOP_NAV);
            } elseif ($navItem->getNav()->getPosition() === NavPosition::Bottom) {
                $this->cache->remove(self::BOTTOM_NAV);
            }
        }
    }
}
