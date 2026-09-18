<?php

namespace App\Admin\Web\Shared\Widget\TextArea;

use Yiisoft\FormModel\FormModel;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

final class TextArea extends Widget
{
    public function __construct(
        private readonly FormModel $form,
        private readonly string $formPropertyName,
        private readonly bool $required,
        private readonly WebView $view,
    ) {}

    public function render(): string
    {
        return $this->view->render(
            __DIR__ . '/template.php',
            [
                'form' => $this->form,
                'formPropertyName' => $this->formPropertyName,
                'required' => $this->required
            ],
        );
    }
}
