<?php

namespace App\Admin\Web\Slug;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Validator\Rule\Required;

final class SlugForm extends FormModel
{
    #[Trim]
    #[Required]
    public string $text = '';

    public function getFormName(): string
    {
        return '';
    }
}
