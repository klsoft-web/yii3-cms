<?php

declare(strict_types=1);

use App\Data\Entities\Category;
use App\Data\Entities\Meta;
use App\Data\Entities\Post;
use App\Data\Entities\Slug;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var Post $post
 * @var Category|null $category
 * @var bool $isHeaderDisplayed
 */

$this->setTitle($post->getName());
/** @var Meta $metaItem */
foreach ($post->getMetaItems() as $metaItem) {
    $this->registerMeta(['name' => $metaItem->getName(), 'content' => $metaItem->getContent()]);
}
?>
<?php
/** @var Slug|null $slug */
$slug = $category?->getSlug();
if ($category !== null && $slug !== null):?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a
                    href="<?= '/' . $slug->getId() ?>"><?= $category->getName() ?></a>
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
