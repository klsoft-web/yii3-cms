<?php

namespace App\Admin\Web\FileBrowser;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;

final class CreateFolderForm extends FormModel
{
    public const  FOLDER_NAME_MAX_LENGTH = 64;

    #[Trim]
    #[Required]
    public string $directory = '';

    #[Trim]
    #[Required]
    #[Regex(pattern: '/^[a-z0-9]+[a-z0-9_-]*[a-z0-9]+$/')]
    #[Length(max: self::FOLDER_NAME_MAX_LENGTH)]
    public string $folder_name = '';

    public function getFormName(): string
    {
        return '';
    }
}
