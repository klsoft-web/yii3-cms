<?php

namespace App\Admin\Web\Nav;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;

final class FindEntitiesForm extends FormModel
{
    #[Required]
    public string $entity_type = '';

    #[Required]
    public string $search = '';

    public function getFormName(): string
    {
        return '';
    }
}
