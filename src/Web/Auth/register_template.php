<?php

declare(strict_types=1);

use App\Messages\App;
use App\Web\Auth\RegisterForm;
use App\Web\Shared\Widget\FormFloatingPasswordInput\FormFloatingPasswordInput;
use App\Web\Shared\Widget\FormFloatingTextInput\FormFloatingTextInput;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @var WebView $this
 * @var Csrf $csrf
 * @var RegisterForm $form
 * @var TranslatorInterface $translator
 * @var string $loginUrl
 */

$this->setTitle($translator->translate(App::SIGN_UP));
?>
<main class="container col-4">
    <h1 class="h3 mb-3"><?= $translator->translate(App::SIGN_UP) ?></h1>
    <?= Html::form()
        ->post()
        ->csrf($csrf)
        ->open() ?>
    <?= FormFloatingTextInput::widget([$form, 'login', App::LOGIN]) ?>
    <?= FormFloatingTextInput::widget([$form, 'email', App::EMAIL]) ?>
    <?= FormFloatingPasswordInput::widget([$form, 'password', App::PASSWORD]) ?>
    <button class="btn btn-primary my-3 w-100" type="submit"><?= $translator->translate(App::SIGN_UP) ?></button>
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
    <?= '</form>' ?> <p class="text-center my-3">
        <?= Html::a($translator->translate(App::SIGN_IN), $loginUrl) ?>
    </p>
</main>
