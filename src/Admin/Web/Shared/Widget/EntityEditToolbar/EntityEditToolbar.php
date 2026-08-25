<?php

namespace App\Admin\Web\Shared\Widget\EntityEditToolbar;

use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

final class EntityEditToolbar extends Widget
{
    public function __construct(
        private readonly string  $listOfEntityRouteName,
        private readonly WebView $view,
    )
    {
    }

    public function render(): string
    {
        return $this->view->render(
            __DIR__ . '/template.php',
            [
                'listOfEntityRouteName' => $this->listOfEntityRouteName
            ]
        );
    }
}
