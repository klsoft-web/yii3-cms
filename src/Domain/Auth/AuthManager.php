<?php

namespace App\Domain\Auth;

use App\Data\Auth\AuthKeyRepositoryInterface;
use App\Data\Auth\Identity;
use App\Data\Entities\User;
use App\Data\User\UserRepositoryInterface;
use App\Data\User\UserStatus;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Translator\TranslatorInterface;
use App\Data\Messages\App;

final readonly class AuthManager implements AuthManagerInterface
{
    public function __construct(
        private UserRepositoryInterface     $userRepository,
        private AuthKeyRepositoryInterface  $authKeyRepository,
        private PasswordHasher              $passwordHasher,
        private TranslatorInterface         $translator,
    ) {}

    public function validateCredentials(string $name, string $password): AuthResult
    {
        $user = $this->userRepository->findByName($name);
        if ($user === null
            || !$this->passwordHasher->validate($password, $user->getPasswordHash())) {
            return new AuthResult(null, null, [$this->translator->translate(App::INCORRECT_LOGIN_OR_PASSWORD)]);
        }
        if ($user->getStatus() === UserStatus::Inactive) {
            return new AuthResult(null, null, [$this->translator->translate(App::THE_USER_IS_INACTIVE)]);
        }

        return new AuthResult(new Identity($user), $user);
    }

    public function saveUser(string $name, string $password, string $email): AuthResult
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPasswordHash($this->passwordHasher->hash($password));
        $user->setAuthKey($this->authKeyRepository->generate());
        $this->userRepository->save($user);

        return new AuthResult(new Identity($user), $user);
    }

    public function refreshAuthKey(User $user): void
    {
        $user->setAuthKey($this->authKeyRepository->generate());
        $this->userRepository->save($user);
    }
}
