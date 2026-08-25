<?php

declare(strict_types=1);

use App\Messages\App;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\Renderer\Csrf;

/**
 * @var UrlGeneratorInterface $urlGenerator
 * @var Csrf $csrf
 * @var TranslatorInterface $translator
 * @var bool $canUserCreateEntity
 * @var bool $canUserDeleteEntity
 * @var string $editEntityRouteName
 * @var string $deleteEntitiesRouteName
 */
?>
<ul class="nav">
    <li class="nav-item<?= $canUserCreateEntity ? '' : ' d-none' ?>">
        <?= Html::tag('button', Html::i('', ['class' => 'bi bi-plus-square nav-item-icon']), ['id' => 'add-entity-btn', 'data-edit-url' => $editEntityRouteName !== '' ? $urlGenerator->generate($editEntityRouteName) : '', 'class' => 'btn', 'title' => $translator->translate(App::ADD_ENTITY)]) ?>
    </li>
    <li class="nav-item<?= $canUserDeleteEntity ? '' : ' d-none' ?>">
        <?= Html::tag('button', Html::i('', ['class' => 'bi bi-trash nav-item-icon']), ['id' => 'delete-entities-btn', 'data-csrf' => $csrf, 'data-delete-url' => $deleteEntitiesRouteName !== '' ? $urlGenerator->generate($deleteEntitiesRouteName) : '', 'class' => 'btn', 'title' => $translator->translate(App::DELETE_SELECTED_RECORDS)]) ?>
    </li>
</ul>
