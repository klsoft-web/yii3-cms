<?php

declare(strict_types=1);

/**
 * @var array<array<string>> $files
 */
?>

<ul class="list-unstyled">
    <?php foreach ($files as $file): ?>
        <li class="file-item mb-3 me-3">
            <?php if ($file['type'] === 'image'): ?>
                <p class="file-item-img-container">
                    <img src="<?= $file['url'] ?>" alt="<?= $file['name'] ?>" />
                </p>
                <p class="m-0">
                    <?= $file['name'] ?>
                    <input type="hidden" class="url <?= $file['type'] ?>" value="<?= $file['url'] ?>" />
                </p>
            <?php elseif ($file['type'] === 'file'): ?>
                <i class="bi bi-file"></i>
                <p class="m-0">
                    <?= $file['name'] ?>
                    <input type="hidden" class="url <?= $file['type'] ?>" value="<?= $file['url'] ?>" />
                </p>
            <?php
            elseif ($file['type'] === 'directory'): ?>
                <i class="bi bi-folder"></i>
                <p class="m-0">
                    <?= $file['name'] ?>
                    <input type="hidden" class="url <?= $file['type'] ?>" value="<?= $file['url'] ?>" />
                </p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
