<?php

declare(strict_types=1);

use App\Data\Entities\Category;
use App\Data\Entities\Post;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var Post $post
 * @var ?Category $category
 * @var bool $isHeaderDisplayed
 */

foreach ($post->getMetaItems() as $metaItem) {
    $this->registerMeta(['name' => $metaItem->getName(), 'content' => $metaItem->getContent()]);
}
$this->setTitle($post->getName());
?>
<?php if ($category !== null): ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a
                    href="<?= '/' . $category->getSlug()->getId() ?>"><?= $category->getName() ?></a>
            </li>
        </ol>
    </nav>
<?php endif; ?>
<?php
if ($isHeaderDisplayed) {
    echo Html::h1($post->getName());
}
?>
<?= $post->getContent() ?>
