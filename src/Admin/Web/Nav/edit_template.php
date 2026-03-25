<?php

declare(strict_types=1);

use App\Admin\Web\Nav\AddNavItemType;
use App\Admin\Web\Nav\NavForm;
use App\Admin\Web\Shared\Layout\Main\EditAsset;
use App\Admin\Web\Shared\Layout\Main\NavAsset;
use App\Admin\Web\Shared\Widget\EntityEditToolbar\EntityEditToolbar;
use App\Admin\Web\Shared\Widget\FormCommonError\FormCommonError;
use Klsoft\Yii3CmsCore\Data\Entities\NavItem;
use Klsoft\Yii3CmsCore\Data\Nav\NavPosition;
use App\Messages\App;
use App\Web\Shared\Widget\FormTextInput\FormTextInput;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Option;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

/**
 * @var WebView $this
 * @var Csrf $csrf
 * @var AssetManager $assetManager
 * @var UrlGeneratorInterface $urlGenerator
 * @var TranslatorInterface $translator
 * @var NavForm $form
 */

$assetManager->register(EditAsset::class);
$assetManager->register(NavAsset::class);
$this->setTitle($translator->translate(App::EDITING_THE_NAVIGATION));
?>
<h1><?= $translator->translate(App::EDITING_THE_NAVIGATION) ?></h1>
<?= Html::form()
    ->post()
    ->csrf($csrf)
    ->open() ?>
<?= Html::hiddenInput('id', $form->id) ?>
<?= FormTextInput::widget([$form, 'name', App::NAME, true]) ?>
<div class="mb-3">
    <label for="nav-items-container" class="form-label"><?= $translator->translate(App::NAVIGATIONS) ?></label>
    <div id="nav-items-container" class="mb-3">
        <?= $this->render(__DIR__ . '/nav_items_template', [
            'navItems' => $form->nav_items
        ]) ?>
    </div>
    <div class="mb-3">
        <label for="add-nav-item-type"
               class="form-label"><?= $translator->translate(App::REFERENCE_TO) ?></label>
        <?= Html::select('add_nav_item_type')->addAttributes([
            'id' => 'add-nav-item-type',
            'class' => 'form-select',
            'placeholder' => '',
            'required' => true,
        ])->value($form->add_nav_item_type)
            ->options(
                Option::tag()->value(AddNavItemType::Page->value)->content($translator->translate(App::PAGE)),
                Option::tag()->value(AddNavItemType::Category->value)->content($translator->translate(App::CATEGORY)),
                Option::tag()->value(AddNavItemType::Url->value)->content($translator->translate(App::URL))) ?>
    </div>

    <div id="add-nav-item-search-container"
         class="mb-3<?= $form->add_nav_item_type === AddNavItemType::Url->value ? ' d-none' : '' ?>">
        <div class="mb-3">
            <input id="add-nav-item-search-entity" type="text" class="form-control"
                   placeholder="<?= $translator->translate(App::SEARCH) ?>"/>
        </div>
        <label for="add-nav-item-search-result"
               class="form-label"><?= $translator->translate(App::SEARCH_RESULTS) ?></label>
        <div id="add-nav-item-search-result">
            <?= $this->render(__DIR__ . '/found_entities_template', ['data' => $form->found_entities]) ?>
        </div>
    </div>
    <div id="add-nav-item-action-name-container"
         class="mb-3<?= $form->add_nav_item_type !== AddNavItemType::Url->value ? ' d-none' : '' ?>">
        <div class="mb-3">
            <label for="nav-item-name"
                   class="form-label"><?= $translator->translate(App::NAVIGATION_ITEM_NAME) ?></label>
            <input id="nav-item-name" type="text" class="form-control">
            <div id="nav-item-name-error" class="invalid-feedback">
                <?= $translator->translate(App::FIELD_NAME_IS_INVALID, ['field_name' => $translator->translate(App::NAVIGATION_ITEM_NAME)]) ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="nav-item-value" class="form-label"><?= $translator->translate(App::URL) ?></label>
            <input id="nav-item-value" type="text" class="form-control" maxlength="<?= NavItem::VALUE_LENGTH ?>"
                   placeholder="/url">
            <div id="nav-item-value-error" class="invalid-feedback">
                <?= $translator->translate(App::FIELD_NAME_IS_INVALID, ['field_name' => $translator->translate(App::URL)]) ?>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <button id="add-nav-item-btn" type="button" class="btn btn-secondary" disabled>
                    <span id="add-nav-item-btn-spinner" class="spinner-border spinner-border-sm d-none"
                          aria-hidden="true"></span>
            <span><?= $translator->translate(App::ADD) ?></span>
        </button>
    </div>
</div>
<div class="mb-3">
    <label for="position" class="form-label"><?= $translator->translate(App::POSITION) ?></label>
    <?= Html::select('position')->addAttributes([
        'id' => 'position',
        'class' => 'form-select',
        'placeholder' => '',
        'required' => true,
    ])->value($form->position)
        ->options(
            Option::tag()->value(NavPosition::Top->value)->content(NavPosition::Top->name),
            Option::tag()->value(NavPosition::Left->value)->content(NavPosition::Left->name),
            Option::tag()->value(NavPosition::Bottom->value)->content(NavPosition::Bottom->name),
            Option::tag()->value(NavPosition::Right->value)->content(NavPosition::Right->name)) ?>
</div>
<?= FormTextInput::widget([$form, 'order', App::ORDER, true]) ?>
<?= EntityEditToolbar::widget(['admin/navs']) ?>
<?= FormCommonError::widget([$form]) ?>
<?= '</form>' ?>
<div id="nav-item-edit-dialog" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <input id="nav-item-key-edit-dialog" type="hidden">
                <div class="mb-3">
                    <label for="nav-item-name-edit-dialog"
                           class="form-label"><?= $translator->translate(App::NAVIGATION_ITEM_NAME) ?></label>
                    <input id="nav-item-name-edit-dialog" type="text" class="form-control">
                    <div id="nav-item-name-edit-dialog-error" class="invalid-feedback">
                        <?= $translator->translate(App::FIELD_NAME_IS_INVALID, ['field_name' => $translator->translate(App::NAVIGATION_ITEM_NAME)]) ?>
                    </div>
                </div>
                <div id="nav-item-value-edit-dialog-container" class="mb-3">
                    <label for="nav-item-value-edit-dialog"
                           class="form-label"><?= $translator->translate(App::URL) ?></label>
                    <input id="nav-item-value-edit-dialog" type="text" class="form-control"
                           maxlength="<?= NavItem::VALUE_LENGTH ?>">
                    <div id="nav-item-value-edit-dialog-error" class="invalid-feedback">
                        <?= $translator->translate(App::FIELD_NAME_IS_INVALID, ['field_name' => $translator->translate(App::URL)]) ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn"
                        data-bs-dismiss="modal"><?= $translator->translate(App::CLOSE) ?></button>
                <button id="nav-item-edit-dialog-apply-btn" type="button"
                        class="btn btn-primary"><?= $translator->translate(App::APPLY) ?></button>
            </div>
        </div>
    </div>
</div>

