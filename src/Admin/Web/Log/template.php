<?php

declare(strict_types=1);

use App\Admin\Web\Shared\Layout\Main\ListAsset;
use Klsoft\Yii3CmsCore\Data\Entities\EntityLog;
use Klsoft\Yii3CmsCore\Data\Log\EntityEventType;
use App\Messages\App;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\Filter\Widget\DropdownFilter;
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
 * @var bool $canUserCreateNav
 * @var bool $canUserUpdateNav
 * @var bool $canUserDeleteNav
 */

$assetManager->register(ListAsset::class);
$this->setTitle($translator->translate(App::LOG));
?>
<h1><?= $translator->translate(App::LOG) ?></h1>
<?= $gridView
    ->dataReader((new OffsetPaginator($dataReader))->withPageSize(20))
    ->containerClass('mt-3')
    ->tableAttributes(['id' => 'entities-table', 'class' => 'table table-bordered'])
    ->columns(
        new DataColumn(
            property: 'date_time',
            header: $translator->translate(App::DATE),
            content: static fn(EntityLog $log) => $log->getDateTime()
        ),
        new DataColumn(
            property: 'event_type',
            header: $translator->translate(App::EVENT),
            content: static fn(EntityLog $log) => $log->getEventType(),
            filter: (new DropdownFilter())
                ->optionsData([
                    EntityEventType::Insert->value => EntityEventType::Insert->name,
                    EntityEventType::Update->value => EntityEventType::Update->name,
                    EntityEventType::Delete->value => EntityEventType::Delete->name])
                ->attributes(['class' => 'form-select'])
        ),
        new DataColumn(
            property: 'entity_class',
            header: $translator->translate(App::ENTITY),
            content: static fn(EntityLog $log) => $log->getEntityClass(),
            filter: true
        ),
        new DataColumn(
            property: 'entity_id',
            header: $translator->translate(App::ENTITY_ID),
            content: static fn(EntityLog $log) => $log->getEntityId(),
            filter: true
        ),
        new DataColumn(
            property: 'user',
            header: $translator->translate(App::USER),
            content: static fn(EntityLog $log) => $log->getUser()->getName(),
        )
    )
?>
