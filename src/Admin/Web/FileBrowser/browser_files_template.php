<?php

declare(strict_types=1);

/**
 * @var array $files
 */
?>

<ul class="list-unstyled">
    <?php foreach ($files as $file): ?>
        <li class="file-item">
            <?php if ($file['type'] === 'image'): ?>
                <img src="<?= $file['url'] ?>" alt="<?= $file['name'] ?>"/>
                <br/>
                <?= $file['name'] ?> <br/>
                <input type="hidden" class="url <?= $file['type'] ?>" value="<?= $file['url'] ?>"/>
            <?php elseif ($file['type'] === 'file'): ?>
                <i class="bi bi-file"></i>
                <br/>
                <?= $file['name'] ?> <br/>
                <input type="hidden" class="url <?= $file['type'] ?>" value="<?= $file['url'] ?>"/>
            <?php
            elseif ($file['type'] === 'directory'): ?>
                <i class="bi bi-folder"></i>
                <br/>
                <?= $file['name'] ?> <br/>
                <input type="hidden" class="url <?= $file['type'] ?>" value="<?= $file['url'] ?>"/>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>
