<?php

declare(strict_types=1);

use App\Admin\Web\Shared\Widget\EntityEditToolbar\EntityEditToolbar;
use App\Admin\Web\Shared\Widget\FormCommonError\FormCommonError;
use App\Admin\Web\User\UserForm;
use App\Admin\Web\Shared\Layout\Main\EditAsset;
use Klsoft\Yii3CmsCore\Data\User\UserStatus;
use App\Messages\App;
use App\Web\Shared\Widget\FormPasswordInput\FormPasswordInput;
use App\Web\Shared\Widget\FormTextInput\FormTextInput;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Option;

/**
 * @var WebView $this
 * @var Csrf $csrf
 * @var AssetManager $assetManager
 * @var UrlGeneratorInterface $urlGenerator
 * @var TranslatorInterface $translator
 * @var UserForm $form
 */

$assetManager->register(EditAsset::class);
$this->setTitle($translator->translate(App::EDITING_THE_USER));
?>
<h1><?= $translator->translate(App::EDITING_THE_USER) ?></h1>
<?= Html::form()
    ->post()
    ->csrf($csrf)
    ->open() ?>
<?= Html::hiddenInput('id', $form->id) ?>
<?= FormTextInput::widget([$form, 'name', App::NAME, true]) ?>
<?= FormTextInput::widget([$form, 'email', App::EMAIL, true]) ?>
<?= FormPasswordInput::widget([$form, 'password', App::PASSWORD]) ?>
<div class="mb-3">
    <label for="status" class="form-label"><?= $translator->translate(App::STATUS) ?></label>
    <?= Html::select('status')->addAttributes([
        'id' => 'status',
        'class' => 'form-select',
        'placeholder' => '',
        'required' => true,
    ])->value($form->status)
        ->options(
            Option::tag()->value(UserStatus::Active->value)->content(UserStatus::Active->name),
            Option::tag()->value(UserStatus::Inactive->value)->content(UserStatus::Inactive->name)) ?>
</div>
<div class="mb-3">
    <label class="form-label"><?= $translator->translate('Roles') ?></label>
    <?php foreach ($form->roles as $name => $isGranted): ?>
        <div class="form-check">
            <?= Html::checkbox('roles[]', $name, ['class' => 'form-check-input', 'checked' => $isGranted]) ?>
            <label class="form-check-label"><?= $name ?></label>
        </div>
    <?php endforeach; ?>
</div>
<?= EntityEditToolbar::widget(['admin/users']) ?>
<?= FormCommonError::widget([$form]) ?>
<?= '</form>' ?>
