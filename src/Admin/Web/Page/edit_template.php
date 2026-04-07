<?php

declare(strict_types=1);

use App\Admin\Web\Page\PageForm;
use App\Admin\Web\Shared\Layout\Main\EditAsset;
use App\Admin\Web\Shared\Layout\Main\SlugAsset;
use App\Admin\Web\Shared\Widget\EntityEditToolbar\EntityEditToolbar;
use App\Admin\Web\Shared\Widget\FormCommonError\FormCommonError;
use App\Data\Post\PostStatus;
use App\Messages\App;
use App\Web\Shared\Widget\FormTextArea\FormTextArea;
use App\Web\Shared\Widget\FormTextInput\FormTextInput;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Tag\Option;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;
use Yiisoft\Html\Html;

/**
 * @var WebView $this
 * @var Csrf $csrf
 * @var AssetManager $assetManager
 * @var UrlGeneratorInterface $urlGenerator
 * @var TranslatorInterface $translator
 * @var PageForm $form
 */

$assetManager->register(EditAsset::class);
$assetManager->register(SlugAsset::class);
$this->setTitle($translator->translate(App::EDITING_THE_PAGE));
?>
<h1><?= $translator->translate(App::EDITING_THE_PAGE) ?></h1>
<?= Html::form()
    ->post()
    ->csrf($csrf)
    ->open() ?>
<?= Html::hiddenInput('id', $form->id) ?>
<?= FormTextInput::widget([$form, 'name', App::NAME, true]) ?>
<?= FormTextInput::widget([$form, 'slug', App::SLUG, true]) ?>
<?= FormTextArea::widget([$form, 'content', App::CONTENT]) ?>
<div class="mb-3">
    <div class="card">
        <div class="card-header">
            Meta
        </div>
        <div class="card-body">
            <?= FormTextInput::widget([$form, 'description', App::DESCRIPTION, false]) ?>
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="status" class="form-label"><?= $translator->translate(App::STATUS) ?></label>
    <?= Html::select('status')->addAttributes([
        'id' => 'status',
        'class' => 'form-select',
        'placeholder' => '',
        'required' => true,
    ])->value($form->status)
        ->options(
            Option::tag()->value(PostStatus::Active->value)->content(PostStatus::Active->name),
            Option::tag()->value(PostStatus::Inactive->value)->content(PostStatus::Inactive->name)) ?>
</div>
<?= EntityEditToolbar::widget(['admin/pages']) ?>
<?= FormCommonError::widget([$form]) ?>
<?= '</form>' ?>

