<?php

declare(strict_types=1);

use App\Data\Entities\Post;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var Post $data
 */
?>
<div class="row border-top py-3">
    <?php if ($data->getSummaryImgPath() !== null): ?>
        <div class="col-sm-3 mb-3">
            <a href="<?= '/' . $data->getSlug()->getId() ?>">
                <img src="<?= $data->getSummaryImgPath() ?>" alt="<?= $data->getName() ?>" class="img-thumbnail">
            </a>
        </div>
    <?php endif; ?>

    <div class="col-sm-9">
        <a class="link-body-emphasis text-decoration-none"
           href="<?= '/' . $data->getSlug()->getId() ?>">
            <h2><?= $data->getName() ?></h2>
            <p>
                <?= $data->getSummary() ?>
            </p>
        </a>
    </div>
</div>


