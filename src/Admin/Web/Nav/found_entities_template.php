<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var array $data
 */
?>

<?php foreach ($data as $item): ?>
    <div class="form-check">
        <?= Html::checkbox(null, $item['id'], ['class' => 'form-check-input']) ?>
        <label class="form-check-label"><?= $item['name'] ?></label>
    </div>
<?php endforeach; ?>
