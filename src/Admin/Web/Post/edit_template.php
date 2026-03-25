<?php

declare(strict_types=1);

use App\Admin\Web\Post\PostForm;
use App\Admin\Web\Shared\Layout\Main\EditAsset;
use App\Admin\Web\Shared\Layout\Main\SlugAsset;
use App\Admin\Web\Shared\Widget\EntityEditToolbar\EntityEditToolbar;
use App\Admin\Web\Shared\Widget\FormCommonError\FormCommonError;
use App\Admin\Web\Shared\Widget\TextArea\TextArea;
use Klsoft\Yii3CmsCore\Data\Entities\Category;
use Klsoft\Yii3CmsCore\Data\Post\PostStatus;
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
 * @var PostForm $form
 */

$assetManager->register(EditAsset::class);
$assetManager->register(SlugAsset::class);
$this->setTitle($translator->translate(App::EDITING_THE_POST));
?>
<h1><?= $translator->translate(App::EDITING_THE_POST) ?></h1>
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
<?= FormTextArea::widget([$form, 'content', App::CONTENT]) ?>
<div class="mb-3">
    <label for="category_id" class="form-label"><?= $translator->translate(App::CATEGORIES) ?></label>
    <?php foreach ($form->categories as $catId => $data): ?>
        <div class="form-check">
            <?= Html::checkbox('categories[]', $catId, ['class' => 'form-check-input', 'checked' => $data['isPostBelongsToCategory']]) ?>
            <label class="form-check-label"><?= $data['name'] ?></label>
        </div>
    <?php endforeach; ?>
</div>
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
<?= EntityEditToolbar::widget(['admin/posts']) ?>
<?= FormCommonError::widget([$form]) ?>
<?= '</form>' ?>

