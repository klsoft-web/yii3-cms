<?php

declare(strict_types=1);

use App\Admin\Web\Shared\Layout\Main\ListAsset;
use App\Admin\Web\Shared\Widget\DeleteEntitiesConfirmDialog;
use App\Admin\Web\Shared\Widget\EntityListToolbar\EntityListToolbar;
use App\Data\User\UserStatus;
use App\Messages\App;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\CheckboxColumn;
use Yiisoft\Yii\View\Renderer\Csrf;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;

/**
 * @var WebView $this
 * @var AssetManager $assetManager
 * @var Csrf $csrf
 * @var UrlGeneratorInterface $urlGenerator
 * @var GridView $gridView
 * @var DataReaderInterface $dataReader
 * @var TranslatorInterface $translator
 * @var bool $canUserCreateUser
 * @var bool $canUserUpdateUser
 * @var bool $canUserDeleteUser
 */

$assetManager->register(ListAsset::class);
$this->setTitle($translator->translate(App::USERS));
?>
<h1><?= $translator->translate(App::USERS) ?></h1>
<?= EntityListToolbar::widget([
    $canUserCreateUser,
    'admin/create-user',
    $canUserDeleteUser,
    'admin/users/delete',
    $csrf
]) ?>
<?= $gridView
    ->dataReader((new OffsetPaginator($dataReader))->withPageSize(20))
    ->containerClass('mt-3')
    ->tableAttributes(['id' => 'entities-table', 'class' => 'table table-bordered'])
    ->bodyRowAttributes(static fn(array $data): array => $canUserUpdateUser ? ['class' => 'entity-item-editable', 'data-edit-url' => $urlGenerator->generate('admin/update-user', ['id' => $data['id']])] : [])
    ->columns(
        new CheckboxColumn(
            headerAttributes: ['id' => 'entities-checkbox-header'],
            inputAttributes: ['class' => 'entity-checkbox-selector'],
            content: static fn(Checkbox $checkbox, DataContext $context) => $checkbox->value($context->data['id']),
            visible: $canUserDeleteUser),
        new DataColumn(
            property: 'name',
            header: $translator->translate(App::NAME),
            filter: true
        ),
        new DataColumn(
            property: 'email',
            header: $translator->translate(App::EMAIL),
            filter: true
        ),
        new DataColumn(
            property: 'status',
            header: $translator->translate(App::STATUS),
            filter: (new DropdownFilter())
                ->optionsData([
                    UserStatus::Active->value => UserStatus::Active->name,
                    UserStatus::Inactive->value => UserStatus::Inactive->name])
                ->attributes(['class' => 'form-select'])
        )
    )
?>
<?= DeleteEntitiesConfirmDialog::widget() ?>
