<?php

declare(strict_types=1);

use App\Admin\Web\Category\CategoryForm;
use App\Admin\Web\Shared\Layout\Main\EditAsset;
use App\Admin\Web\Shared\Layout\Main\SlugAsset;
use App\Admin\Web\Shared\Widget\EntityEditToolbar\EntityEditToolbar;
use App\Admin\Web\Shared\Widget\FormCommonError\FormCommonError;
use App\Admin\Web\Shared\Widget\TextArea\TextArea;
use App\Messages\App;
use App\Web\Shared\Widget\FormTextInput\FormTextInput;
use Yiisoft\Assets\AssetManager;
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
 * @var CategoryForm $form
 */

$assetManager->register(EditAsset::class);
$assetManager->register(SlugAsset::class);
$this->setTitle($translator->translate(App::EDITING_THE_CATEGORY));
?>
<h1><?= $translator->translate(App::EDITING_THE_CATEGORY) ?></h1>
<?= Html::form()
    ->post()
    ->csrf($csrf)
    ->open() ?>
<?= Html::hiddenInput('id', $form->id) ?>
<?= FormTextInput::widget([$form, 'name', App::NAME, true]) ?>
<?= FormTextInput::widget([$form, 'slug', App::SLUG, true]) ?>
<div class="mb-3">
    <label for="summary-container" class="form-label"><?= $translator->translate(App::SUMMARY) ?></label>
    <div id="summary-container" class="row">
        <div class="col-auto">
            <?= Html::hiddenInput('summary_img_path', $form->summary_img_path, ['id' => 'summary-img-path-value']) ?>
            <i id="summary-img-path-icon"
               class="bi bi-image summary-img-icon<?= $form->summary_img_path !== null ? ' d-none' : '' ?>"></i>
            <img id="summary-img-path-img" src="<?= $form->summary_img_path ?>" alt="" class="summary-img">
            <div class="my-3">
                <div class="btn-group" role="group">
                    <button id="edit-summary-img-btn" type="button" class="btn btn-outline-secondary"><i
                            class="bi bi-pencil nav-item-edit"></i>
                    </button>
                    <button id="remove-summary-img-btn" type="button" class="btn btn-outline-secondary"><i
                            class="bi bi-trash nav-item-remove"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col">
            <?= TextArea::widget([$form, 'summary', false]) ?>
        </div>
    </div>
</div>
<div class="mb-3">
    <div class="card">
        <div class="card-header">
            Meta
        </div>
        <div class="card-body">
            <?= FormTextInput::widget([$form, 'description', $translator->translate(App::DESCRIPTION), false]) ?>
        </div>
    </div>
</div>
<?= FormTextInput::widget([$form, 'order', App::ORDER, true]) ?>
<?= EntityEditToolbar::widget(['admin/categories']) ?>
<?= FormCommonError::widget([$form]) ?>
<?= '</form>' ?>

