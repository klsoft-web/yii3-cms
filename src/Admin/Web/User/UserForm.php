<?php

namespace App\Admin\Web\User;

use App\Admin\Data\User\UserAdminRepositoryInterface;
use App\Data\Auth\AuthRepositoryInterface;
use App\Data\Entities\User;
use App\Data\User\UserStatus;
use App\Messages\App;
use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;

final class UserForm extends FormModel
{
    public function __construct(
        private readonly UserAdminRepositoryInterface $userAdminRepository,
        private readonly AuthRepositoryInterface      $authRepository,
        private readonly TranslatorInterface          $translator)
    {
    }

    #[Safe]
    public ?int $id = null;

    #[Trim]
    #[Required]
    #[Regex(pattern: '/^\w+(.)*/u')]
    #[Length(max: User::NAME_LENGTH)]
    #[Callback(method: 'validateName')]
    public string $name = '';

    #[Trim]
    #[Required]
    #[Email]
    #[Callback(method: 'validateEmail')]
    public string $email = '';

    #[Trim]
    #[Required]
    #[Callback(method: 'validatePassword')]
    public string $password = '';

    #[Required]
    public string $status = UserStatus::Active->value;

    #[Safe]
    public array $roles = [];

    private function validateName(): Result
    {
        $result = new Result();
        $user = $this->userAdminRepository->findByNameOrEmail($this->name, $this->email);
        if ($user !== null &&
            $user->getId() !== $this->id &&
            $user->getName() === $this->name) {
            $result->addError($this->translator->translate(
                App::THE_RECORD_WITH_THE_FIELD_NAME_ALREADY_EXISTS,
                ['field_name' => strtolower($this->translator->translate(App::NAME))]));
        }
        return $result;
    }

    private function validateEmail(): Result
    {
        $result = new Result();
        $user = $this->userAdminRepository->findByNameOrEmail($this->name, $this->email);
        if ($user !== null &&
            $user->getId() !== $this->id &&
            $user->getEmail() === $this->email) {
            $result->addError($this->translator->translate(
                App::THE_RECORD_WITH_THE_FIELD_NAME_ALREADY_EXISTS,
                ['field_name' => strtolower($this->translator->translate('Email'))]));
        }
        return $result;
    }

    private function validatePassword(): Result
    {
        $result = new Result();
        if (mb_strlen($this->password) < $this->authRepository->getMinPasswordLength()) {
            $result->addError($this->translator->translate(
                App::THE_PASSWORD_IS_SHORTER_THAN_THE_MINIMUM_REQUIRED_LENGTH,
                ['length' => $this->authRepository->getMinPasswordLength()]));
        }
        return $result;
    }

    public function getFormName(): string
    {
        return '';
    }
}
