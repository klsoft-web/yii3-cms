<?php

namespace App\Admin\Web\FileBrowser;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Validator\Rule\Required;

final class FilesForm extends FormModel
{
    #[Trim]
    #[Required]
    public string $directory = '';

    public function getFormName(): string
    {
        return '';
    }
}
