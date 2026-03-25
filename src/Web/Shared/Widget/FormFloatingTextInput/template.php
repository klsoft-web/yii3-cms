<?php

declare(strict_types=1);

use Yiisoft\FormModel\FormModel;
use Yiisoft\Html\Html;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @var FormModel $form
 * @var string $formPropertyName
 * @var string $formPropertyLabel
 * @var TranslatorInterface $translator
 */
?>

<div class="form-floating">
    <?= Html::textInput($formPropertyName, $form->getPropertyValue($formPropertyName))
        ->addAttributes([
            'id' => $formPropertyName,
            'class' => 'form-control' . ($form->isValidated() &&
                !empty($form->getValidationResult()->getPropertyErrorMessages($formPropertyName)) ? ' is-invalid' : ''),
            'placeholder' => '',
            'required' => true,
        ]) ?>
    <label for="<?= $formPropertyName ?>" class="form-label"><?= $translator->translate($formPropertyLabel) ?></label>
    <?php
    if ($form->isValidated() &&
        !empty($form->getValidationResult()->getPropertyErrorMessages($formPropertyName))) {
        echo Html::div(
            implode(
                '<br>',
                array_map(
                    Html::encode(...),
                    $form->getValidationResult()->getPropertyErrorMessages($formPropertyName)
                )
            ),
            ['class' => 'invalid-feedback'])
            ->encode(false);
    }
    ?>
</div>
