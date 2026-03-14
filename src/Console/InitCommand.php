<?php

namespace App\Console;

use App\Admin\Data\Role\RoleRepositoryInterface;
use App\Data\Auth\AuthKeyRepositoryInterface;
use App\Data\Auth\AuthRepositoryInterface;
use App\Data\Entities\Post;
use App\Data\Entities\Slug;
use App\Data\Entities\User;
use App\Data\Post\PostStatus;
use App\Data\Post\PostType;
use App\Data\Rbac\Permission;
use App\Data\Site\SiteRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Security\Random;
use Yiisoft\Yii\Console\Application;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Role;
use Yiisoft\Yii\Console\ExitCode;

#[AsCommand(
    name: 'init',
    description: 'Initialize the Yii3-CMS',
)]
final class InitCommand extends Command
{
    private const ROLE_ADMIN = 'admin';
    private const USER_ADMIN = 'admin';
    private const EMAIL_ADMIN = 'admin@example.com';

    public function __construct(
        private readonly Application                $application,
        private readonly ManagerInterface           $manager,
        private readonly RoleRepositoryInterface    $roleRepository,
        private readonly AuthRepositoryInterface    $authRepository,
        private readonly AuthKeyRepositoryInterface $authKeyRepository,
        private readonly EntityManagerInterface     $entityManager,
        private readonly SiteRepositoryInterface    $siteRepository,
        private readonly PasswordHasher             $passwordHasher)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $greetInput = new ArrayInput([
            'command' => 'doctrine:orm:schema-tool:create'
        ]);
        $greetInput->setInteractive(false);

        if ($this->application->doRun($greetInput, $output) === 0) {
            $this->manager->addRole(new Role(self::ROLE_ADMIN));
            foreach ($this->roleRepository->getGroupsOfPermissions() as $permissions) {
                foreach ($permissions as $permission) {
                    if ($permission->getName() !== Permission::UPDATE_ONLY_YOUR_POSTS &&
                        $permission->getName() !== Permission::UPDATE_ONLY_YOUR_PAGES &&
                        $permission->getName() !== Permission::UPDATE_ONLY_YOUR_CATEGORIES &&
                        $permission->getName() !== Permission::UPDATE_ONLY_YOUR_NAVIGATIONS) {
                        $this->manager->addPermission($permission);
                        $this->manager->addChild(self::ROLE_ADMIN, $permission->getName());
                    }
                }
            }
            $password = Random::string($this->authRepository->getMinPasswordLength());
            $user = new User();
            $user->setName(self::USER_ADMIN);
            $user->setEmail(self::EMAIL_ADMIN);
            $user->setPasswordHash($this->passwordHasher->hash($password));
            $user->setAuthKey($this->authKeyRepository->generate());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->manager->assign(self::ROLE_ADMIN, $user->getId());

            $output->writeln('<info>Yii3-CMS has been successfully initialized!</info>');
            $output->writeln([
                'You can use the following credentials to access the admin panel: /admin/pages',
                'Login:' . self::USER_ADMIN,
                'Password:' . $password
            ]);

            $slug = new Slug();
            $slug->setId($this->siteRepository->getHomePageSlug());
            $slug->setEntityClass(Post::class);
            $post = new Post();
            $post->setSlug($slug);
            $post->setPostType(PostType::Page);
            $post->setStatus(PostStatus::Active);
            $post->setName('My site');
            $post->setDateTime(new DateTimeImmutable());
            $post->setContent('This is the homepage content that was created during initialization.</br>You can edit it using the <a href="/admin/pages">admin panel</a>.');
            $post->setCreatedByUser($user);
            $this->entityManager->persist($slug);
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return ExitCode::OK;
        }

        return ExitCode::UNSPECIFIED_ERROR;
    }
}
