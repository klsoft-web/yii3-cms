<?php

namespace App\Admin\Web\Shared\Widget\EntityListToolbar;

use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\View\Renderer\Csrf;

final class EntityListToolbar extends Widget
{
    public function __construct(
        private readonly bool    $canUserCreateEntity = false,
        private readonly string  $editEntityRouteName = '',
        private readonly bool    $canUserDeleteEntity = false,
        private readonly string  $deleteEntitiesRouteName = '',
        private readonly Csrf $csrf,
        private readonly WebView $view,
    )
    {
    }

    public function render(): string
    {
        return $this->view->render(
            __DIR__ . '/template.php',
            [
                'canUserCreateEntity' => $this->canUserCreateEntity,
                'editEntityRouteName' => $this->editEntityRouteName,
                'canUserDeleteEntity' => $this->canUserDeleteEntity,
                'deleteEntitiesRouteName' => $this->deleteEntitiesRouteName,
                'csrf' => $this->csrf
            ],
        );
    }
}
