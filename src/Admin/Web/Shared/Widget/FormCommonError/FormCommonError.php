<?php

namespace App\Admin\Web\Shared\Widget\FormCommonError;

use Yiisoft\FormModel\FormModel;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

final class FormCommonError extends Widget
{
    public function __construct(
        private readonly FormModel $form,
        private readonly WebView $view,
    ) {}

    public function render(): string
    {
        return $this->view->render(
            __DIR__ . '/template.php',
            [
                'form' => $this->form
            ],
        );
    }
}
