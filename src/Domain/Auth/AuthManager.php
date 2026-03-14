<?php

namespace App\Domain\Auth;

use App\Data\Auth\AuthKeyRepositoryInterface;
use App\Data\Entities\User;
use App\Data\User\UserRepositoryInterface;
use App\Data\User\UserStatus;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\Translator\TranslatorInterface;
use App\Messages\App;

final readonly class AuthManager implements AuthManagerInterface
{
    public function __construct(
        private UserRepositoryInterface     $userRepository,
        private IdentityRepositoryInterface $identityRepository,
        private AuthKeyRepositoryInterface  $authKeyRepository,
        private PasswordHasher              $passwordHasher,
        private TranslatorInterface         $translator)
    {
    }

    public function validateCredentialsThenFindIdentity(string $name, string $password): AuthResult
    {
        $user = $this->userRepository->findByName($name);
        if ($user === null ||
            !$this->passwordHasher->validate($password, $user->getPasswordHash())) {
            return new AuthResult(null, null, [$this->translator->translate(App::INCORRECT_LOGIN_OR_PASSWORD)]);
        }

        if ($user->getStatus() === UserStatus::Inactive) {
            return new AuthResult(null, null, [$this->translator->translate(App::THE_USER_IS_INACTIVE)]);
        }

        $identity = $this->identityRepository->findIdentity($user->getId());
        if ($identity === null) {
            return new AuthResult(null, null, [$this->translator->translate(App::SIGN_IN_FAILED)]);
        }
        return new AuthResult($identity, $user);
    }

    public function saveUserThenFindIdentity(string $name, string $password, string $email): AuthResult
    {
        if ($this->userRepository->findByNameOrEmail($name, $email) !== null) {
            return new AuthResult(null, null, [$this->translator->translate(App::THE_USER_LOGIN_OR_EMAIL_ALREADY_EXISTS)]);
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPasswordHash($this->passwordHasher->hash($password));
        $user->setAuthKey($this->authKeyRepository->generate());
        $this->userRepository->save($user);

        $identity = $this->identityRepository->findIdentity($user->getId());
        if ($identity === null) {
            return new AuthResult(null, null, [$this->translator->translate(App::SIGN_IN_FAILED)]);
        }
        return new AuthResult($identity, $user);
    }

    public function refreshAuthKey(User $user): void
    {
        $user->setAuthKey($this->authKeyRepository->generate());
        $this->userRepository->save($user);
    }
}
