<?php

namespace App\Admin\Data\User;

use App\Admin\Data\Shared\EntityChangedResult;
use App\Data\Entities\User;
use App\Data\Log\EntityEventType;
use Doctrine\ORM\EntityManagerInterface;
use Klsoft\Yii3RbacDoctrine\Entities\YiiRbacItem;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ManagerInterface;

final readonly class UserAdminRepository implements UserAdminRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ManagerInterface       $manager)
    {
    }

    public function create(): UserWithRoles
    {
        $roles = [];
        $allRoles = $this->getAllRoles();
        foreach ($allRoles as $role) {
            $roles[$role['name']] = false;
        }
        return new UserWithRoles(new User(), $roles);
    }

    private function getAllRoles(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('r.name')
            ->from(YiiRbacItem::class, 'r')
            ->where('r.type = :type')
            ->setParameter('type', Item::TYPE_ROLE)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(UserWithRoles $userWithRoles): EntityChangedResult
    {
        $isNewEntity = $userWithRoles->user->getId() === null;
        $this->entityManager->persist($userWithRoles->user);
        $this->entityManager->flush();

        $this->manager->revokeAll($userWithRoles->user->getId());
        foreach ($userWithRoles->roles as $name => $isGranted) {
            if ($isGranted) {
                $this->manager->assign($name, $userWithRoles->user->getId());
            }
        }

        return new EntityChangedResult($userWithRoles->user, $isNewEntity ? EntityEventType::Insert : EntityEventType::Update);
    }

    public function delete(array $ids): array
    {
        $deletedEntities = [];
        foreach ($ids as $id) {
            $user = $this->entityManager->find(User::class, $id);
            if ($user !== null) {
                $userRemoved = clone $user;
                $this->entityManager->remove($user);
                $deletedEntities[] = new EntityChangedResult($userRemoved, EntityEventType::Delete);
            }
        }
        $this->entityManager->flush();

        return $deletedEntities;
    }

    public function find(string $id): UserWithRoles
    {
        $user = $this->entityManager->find(User::class, $id);
        $rolesByUserId = [];
        if ($user !== null) {
            $rolesByUserId = array_map(fn($role) => $role->getName(), $this->manager->getRolesByUserId($user->getId()));
        }
        $roles = [];
        $allRoles = $this->getAllRoles();
        foreach ($allRoles as $role) {
            $roles[$role['name']] = in_array($role['name'], $rolesByUserId);
        }
        return new UserWithRoles($user ?? new User(), $roles);
    }

    public function findByNameOrEmail(string $name, string $email): ?User
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.name = :name')
            ->orWhere('u.email = :email')
            ->setParameter('name', $name)
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
