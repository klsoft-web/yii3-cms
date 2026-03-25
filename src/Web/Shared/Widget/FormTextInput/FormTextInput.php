<?php

namespace App\Web\Shared\Widget\FormTextInput;

use Yiisoft\FormModel\FormModel;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

final class FormTextInput extends Widget
{
    public function __construct(
        private readonly FormModel $form,
        private readonly string    $formPropertyName,
        private readonly string    $formPropertyLabel,
        private readonly bool      $required,
        private readonly WebView   $view,
    )
    {
    }

    public function render(): string
    {
        return $this->view->render(
            __DIR__ . '/template.php',
            [
                'form' => $this->form,
                'formPropertyName' => $this->formPropertyName,
                'formPropertyLabel' => $this->formPropertyLabel,
                'required' => $this->required
            ],
        );
    }
}
