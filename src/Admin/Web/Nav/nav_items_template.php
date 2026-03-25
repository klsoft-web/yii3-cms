<?php

declare(strict_types=1);

use Symfony\Component\Uid\UuidV7;
use Yiisoft\Html\Html;

/**
 * @var array $navItems
 */
?>

<?php foreach ($navItems as $navItem): ?>
    <?php
    $navItemKey = (new UuidV7())->toString();
    ?>
    <div class="card text-bg-secondary mb-2 nav-item" data-key="<?= $navItemKey ?>">
        <div class="card-header">
            <div class="row">
                <div class="col d-flex align-content-center flex-wrap">
                    <span class="nav-item-text"><?= $navItem->getName() ?></span>
                    <?= Html::hiddenInput("nav_items[" . $navItemKey . "][id]", $navItem->getId()) ?>
                    <?= Html::hiddenInput("nav_items[" . $navItemKey . "][nav_item_type]", $navItem->getNavItemType()->value) ?>
                    <?= Html::hiddenInput("nav_items[" . $navItemKey . "][name]", $navItem->getName()) ?>
                    <?= Html::hiddenInput("nav_items[" . $navItemKey . "][value]", $navItem->getValue()) ?>
                    <?= Html::hiddenInput("nav_items[" . $navItemKey . "][order]", $navItem->getOrder()) ?>
                </div>
                <div class="col-md-auto d-flex align-content-center flex-wrap">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-light nav-item-up"><i
                                class="bi bi-arrow-up"></i>
                        </button>
                        <button type="button" class="btn btn-outline-light nav-item-edit"><i
                                class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-light nav-item-remove"><i
                                class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>



