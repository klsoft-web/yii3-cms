<?php

namespace App\Web\Shared\Widget\FormFloatingTextInput;

use Yiisoft\FormModel\FormModel;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

final class FormFloatingTextInput extends Widget
{
    public function __construct(
        private readonly FormModel $form,
        private readonly string $formPropertyName,
        private readonly string $formPropertyLabel,
        private readonly WebView $view,
    ) {}

    public function render(): string
    {
        return $this->view->render(
            __DIR__ . '/template.php',
            [
                'form' => $this->form,
                'formPropertyName' => $this->formPropertyName,
                'formPropertyLabel' => $this->formPropertyLabel,
            ],
        );
    }
}
