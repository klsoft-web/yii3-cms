<?php

declare(strict_types=1);

use App\Admin\Web\Role\RoleForm;
use App\Admin\Web\Shared\Layout\Main\EditAsset;
use App\Admin\Web\Shared\Widget\EntityEditToolbar\EntityEditToolbar;
use App\Admin\Web\Shared\Widget\FormCommonError\FormCommonError;
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
 * @var RoleForm $form
 */

$assetManager->register(EditAsset::class);
$this->setTitle($translator->translate(App::EDITING_THE_ROLE));
?>
<h1><?= $translator->translate(App::EDITING_THE_ROLE) ?></h1>
<?= Html::form()
    ->post()
    ->csrf($csrf)
    ->open() ?>
<?= Html::hiddenInput('id', $form->id) ?>
<?= FormTextInput::widget([$form, 'name', App::NAME, true]) ?>
<div class="mb-3">
    <label class="form-label"><?= $translator->translate(App::PERMISSIONS) ?></label>
    <ul class="list-group">
        <?php foreach ($form->permissions as $group => $permissions): ?>
            <li class="list-group-item">
                <?= $group ?>
                <ul class="list-unstyled">
                    <?php foreach ($permissions as $name => $isGranted): ?>
                        <li>
                            <?= Html::checkbox('permissions[]', $name, ['class' => 'form-check-input', 'checked' => $isGranted]) ?>
                            <label class="form-check-label"><?= $translator->translate($name) ?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?= EntityEditToolbar::widget(['admin/roles']) ?>
<?= FormCommonError::widget([$form]) ?>
<?= '</form>' ?>

