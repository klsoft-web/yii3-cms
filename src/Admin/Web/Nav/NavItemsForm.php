<?php

namespace App\Admin\Web\Nav;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;

final class NavItemsForm extends FormModel
{
    #[Required]
    public string $nav_item_type = '';

    /** @var array<array<string, string>> $nav_items */
    #[Required]
    public array $nav_items = [];

    public function getFormName(): string
    {
        return '';
    }
}
