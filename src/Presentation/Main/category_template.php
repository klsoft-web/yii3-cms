<?php

declare(strict_types=1);

use App\Data\Entities\Category;
use App\Data\Entities\Meta;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\CountableDataInterface;
use Yiisoft\Data\Reader\LimitableDataInterface;
use Yiisoft\Data\Reader\OffsetableDataInterface;
use Yiisoft\Data\Reader\ReadableDataInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView\ListView;

/**
 * @var WebView $this
 * @var Category $category
 * @var ListView $listView
 * @var ReadableDataInterface&LimitableDataInterface&OffsetableDataInterface&CountableDataInterface $dataReader
 */

$this->setTitle($category->getName());
/** @var Meta $metaItem */
foreach ($category->getMetaItems() as $metaItem) {
    $this->registerMeta(['name' => $metaItem->getName(), 'content' => $metaItem->getContent()]);
}

?>
<h1 class="b-3"><?= $category->getName() ?></h1>
<?= $listView
    ->dataReader((new OffsetPaginator($dataReader))->withPageSize(20))
    ->itemView(__DIR__ . '/post_item_template')
    ->listAttributes(['class' => 'list-unstyled px-1'])
?>

