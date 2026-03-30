<?php

declare(strict_types=1);

use App\Messages\App;
use App\Web\Auth\LoginForm;
use App\Web\Shared\Widget\FormFloatingPasswordInput\FormFloatingPasswordInput;
use App\Web\Shared\Widget\FormFloatingTextInput\FormFloatingTextInput;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @var WebView $this
 * @var Csrf $csrf
 * @var LoginForm $form
 * @var TranslatorInterface $translator
 * @var string $registerUrl
 */

$this->setTitle($translator->translate(App::SIGN_IN));
?>
<main class="container col-4">
    <h1 class="h3 mb-3"><?= $translator->translate(App::SIGN_IN) ?></h1>
    <?= Html::form()
        ->post()
        ->csrf($csrf)
        ->open() ?>
    <?= FormFloatingTextInput::widget([$form, 'login', App::LOGIN]) ?>
    <?= FormFloatingPasswordInput::widget([$form, 'password', App::PASSWORD]) ?>
    <div class="form-check text-start my-3">
        <?= Html::checkbox('rememberMe')
            ->class('form-check-input')
            ->id('remember-me')
            ->checked($form->rememberMe) ?>
        <label class="form-check-label" for="remember-me">
            <?= $translator->translate(App::REMEMBER_ME) ?>
        </label>
    </div>
    <button class="btn btn-primary w-100" type="submit"><?= $translator->translate(App::SIGN_IN) ?></button>
    <?php
    if ($form->isValidated() && !$form->isValid()) {
        echo Html::div(
            implode(
                '<br>',
                array_map(
                    Html::encode(...),
                    $form->getValidationResult()->getErrorMessages(),
                ),
            ),
            ['class' => 'text-bg-danger p-3 mt-4'],
        )->encode(false);
    }
    ?>
    <?= '</form>' ?>
</main>
