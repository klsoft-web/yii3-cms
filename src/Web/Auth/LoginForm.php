<?php

namespace App\Web\Auth;

use Klsoft\Yii3CmsCore\Data\Entities\User;
use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    public function __construct()
    {
    }

    #[Trim]
    #[Required]
    #[Length(max: User::NAME_LENGTH)]
    public string $login = '';

    #[Trim]
    #[Required]
    public string $password = '';

    #[Safe]
    public bool $rememberMe = false;


    public function getFormName(): string
    {
        return '';
    }
}
