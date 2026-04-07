<?php

declare(strict_types=1);

use App\Admin\Web\Shared\Layout\Main\ListAsset;
use App\Admin\Web\Shared\Widget\DeleteEntitiesConfirmDialog;
use App\Admin\Web\Shared\Widget\EntityListToolbar\EntityListToolbar;
use App\Data\Entities\Category;
use App\Messages\App;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
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
 * @var bool $canUserCreateCategory
 * @var bool $canUserUpdateCategory
 * @var bool $canUserDeleteCategory
 */

$assetManager->register(ListAsset::class);
$this->setTitle($translator->translate(App::CATEGORIES));
?>
<h1><?= $translator->translate(App::CATEGORIES) ?></h1>
<?= EntityListToolbar::widget([
    $canUserCreateCategory,
    'admin/create-category',
    $canUserDeleteCategory,
    'admin/categories/delete',
    $csrf
]) ?>
<?= $gridView
    ->dataReader((new OffsetPaginator($dataReader))->withPageSize(20))
    ->containerClass('mt-3')
    ->tableAttributes(['id' => 'entities-table', 'class' => 'table table-bordered'])
    ->bodyRowAttributes(static fn(Category $category): array => $canUserUpdateCategory ? ['class' => 'entity-item-editable', 'data-edit-url' => $urlGenerator->generate('admin/update-category', ['id' => $category->getSlug()->getId()])] : [])
    ->columns(
        new CheckboxColumn(
            headerAttributes: ['id' => 'entities-checkbox-header'],
            inputAttributes: ['class' => 'entity-checkbox-selector'],
            content: static fn(Checkbox $checkbox, DataContext $context) => $checkbox->value($context->data->getSlug()->getId()),
            visible: $canUserDeleteCategory),
        new DataColumn(
            property: 'name',
            header: $translator->translate(App::NAME),
            content: static fn(Category $category) => $category->getName(),
            filter: true
        ),
        new DataColumn(
            property: 'slug',
            header: $translator->translate(App::SLUG),
            content: static fn(Category $category) => $category->getSlug()->getId()
        ),
        new DataColumn(
            property: 'order',
            header: $translator->translate(App::ORDER),
            content: static fn(Category $category) => $category->getOrder(),
            filter: true)
    )
?>
<?= DeleteEntitiesConfirmDialog::widget() ?>
