<?php

declare(strict_types=1);

use App\Data\Entities\Category;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\ListView\ListView;

/**
 * @var WebView $this
 * @var Category $category
 * @var ListView $listView
 * @var DataReaderInterface $dataReader
 * @var
 */

foreach ($category->getMetaItems() as $metaItem) {
    $this->registerMeta(['name' => $metaItem->getName(), 'content' => $metaItem->getContent()]);
}
$this->setTitle($category->getName());
?>
<h1 class="mb-3"><?= $category->getName() ?></h1>
<?= $listView
    ->dataReader((new OffsetPaginator($dataReader))->withPageSize(20))
    ->itemView(__DIR__ . '/post_item_template')
    ->listAttributes(['class' => 'list-unstyled px-1'])
?>

