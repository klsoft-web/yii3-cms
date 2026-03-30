<?php

namespace App\Admin\Web\Shared\Form;

use _PHPStan_f1e88529a\Symfony\Contracts\Service\Attribute\Required;
use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;

final class DeleteEntitiesForm extends FormModel
{
    #[Safe]
    public array $delete_ids = [];

    public function getFormName(): string
    {
        return '';
    }
}
