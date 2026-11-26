<?php

declare(strict_types=1);


use App\Data\Entities\PostCategory;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var PostCategory $data
 */
?>
<?php
/** @var string $postId */
$postId = $data->getPost()->getSlug()?->getId();
/** @var string $categoryId */
$categoryId = $data->getCategory()->getSlug()?->getId();
?>
<div class="row border-top py-3">
    <?php if ($data->getPost()->getSummaryImgPath() !== null): ?>
        <div class="col-sm-3 mb-3">
            <a href="<?= '/' . $categoryId . '/' . $postId ?>">
                <img src="<?= $data->getPost()->getSummaryImgPath() ?>" alt="<?= $data->getPost()->getName() ?>"
                     class="img-thumbnail">
            </a>
        </div>
    <?php endif; ?>

    <div class="col-sm-9">
        <a class="link-body-emphasis text-decoration-none"
           href="<?= '/' . $categoryId . '/' . $postId ?>">
            <h2><?= $data->getPost()->getName() ?></h2>
            <p>
                <?= $data->getPost()->getSummary() ?>
            </p>
        </a>
    </div>
</div>


