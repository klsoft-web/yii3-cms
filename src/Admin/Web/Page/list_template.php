<?php

declare(strict_types=1);

use App\Admin\Web\Shared\Layout\Main\ListAsset;
use App\Admin\Web\Shared\Widget\DeleteEntitiesConfirmDialog;
use App\Admin\Web\Shared\Widget\EntityListToolbar\EntityListToolbar;
use App\Data\Entities\Post;
use App\Data\Post\PostStatus;
use App\Messages\App;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
use Yiisoft\Yii\View\Renderer\Csrf;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\CheckboxColumn;
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
 * @var bool $canUserCreatePage
 * @var bool $canUserUpdatePage
 * @var bool $canUserDeletePage
 */

$assetManager->register(ListAsset::class);
$this->setTitle($translator->translate(App::PAGES));
?>
<h1><?= $translator->translate(App::PAGES) ?></h1>
<?= EntityListToolbar::widget([
    $canUserCreatePage,
    'admin/create-page',
    $canUserDeletePage,
    'admin/pages/delete',
    $csrf
]) ?>
<?= $gridView
    ->dataReader((new OffsetPaginator($dataReader))->withPageSize(20))
    ->containerClass('mt-3')
    ->tableAttributes(['id' => 'entities-table', 'class' => 'table table-bordered'])
    ->bodyRowAttributes(static fn(Post $post): array => $canUserUpdatePage ? ['class' => 'entity-item-editable', 'data-edit-url' => $urlGenerator->generate('admin/update-page', ['id' => $post->getSlug()->getId()])] : [])
    ->columns(
        new CheckboxColumn(
            headerAttributes: ['id' => 'entities-checkbox-header'],
            inputAttributes: ['class' => 'entity-checkbox-selector'],
            content: static fn(Checkbox $checkbox, DataContext $context) => $checkbox->value($context->data->getSlug()->getId()),
            visible: $canUserDeletePage),
        new DataColumn(
            property: 'name',
            header: $translator->translate(App::NAME),
            content: static fn(Post $post) => $post->getName(),
            filter: true
        ),
        new DataColumn(
            property: 'slug',
            header: $translator->translate(App::SLUG),
            content: static fn(Post $post) => $post->getSlug()->getId()
        ),
        new DataColumn(
            property: 'status',
            header: $translator->translate(App::STATUS),
            content: static fn(Post $post) => $post->getStatus(),
            filter: (new DropdownFilter())
                ->optionsData([
                    PostStatus::Active->value => PostStatus::Active->name,
                    PostStatus::Inactive->value => PostStatus::Inactive->name])
                ->attributes(['class' => 'form-select'])
        )
    )
?>
<?= DeleteEntitiesConfirmDialog::widget() ?>
