<?php

namespace App\Web\Auth;

use App\Data\Auth\AuthRepositoryInterface;
use App\Data\User\UserRepositoryInterface;
use Klsoft\Yii3CmsCore\Data\Entities\User;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Translator\TranslatorInterface;
use App\Messages\App;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class RegisterForm extends FormModel
{
    public function __construct(
        private readonly AuthRepositoryInterface $authRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TranslatorInterface     $translator)
    {
    }

    #[Trim]
    #[Required]
    #[Length(max: User::NAME_LENGTH)]
    #[Callback(method: 'validateLogin')]
    public string $login = '';

    #[Trim]
    #[Required]
    #[Email]
    #[Callback(method: 'validateEmail')]
    public string $email = '';

    #[Trim]
    #[Required]
    #[Callback(method: 'validatePassword')]
    public string $password = '';

    private function validateLogin(): Result
    {
        $result = new Result();
        if ($this->userRepository->findByName($this->login) !== null) {
            $result->addError($this->translator->translate(
                App::THE_USER_FIELD_NAME_ALREADY_EXISTS,
                ['field_name' => $this->translator->translate(App::LOGIN)]));
        }
        return $result;
    }

    private function validateEmail(): Result
    {
        $result = new Result();
        if ($this->userRepository->findByEmail($this->email) !== null) {
            $result->addError($this->translator->translate(
                App::THE_USER_FIELD_NAME_ALREADY_EXISTS,
                ['field_name' => $this->translator->translate(App::EMAIL)]));
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
