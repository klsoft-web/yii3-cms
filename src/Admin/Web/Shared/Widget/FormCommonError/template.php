<?php

declare(strict_types=1);

use Yiisoft\FormModel\FormModel;
use Yiisoft\Html\Html;

/**
 * @var FormModel $form
 */
?>

<?php
if ($form->isValidated() &&
    !empty($form->getValidationResult()->getCommonErrorMessages())) {
    echo Html::div(
        implode(
            '<br>',
            array_map(
                Html::encode(...),
                $form->getValidationResult()->getCommonErrorMessages()
            )
        ),
        ['class' => 'text-bg-danger p-3 mt-3'])
        ->encode(false);
}
?>
