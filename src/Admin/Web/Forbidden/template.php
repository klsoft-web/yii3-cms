<?php

declare(strict_types=1);

use App\Messages\App;
use Yiisoft\View\WebView;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 * @var TranslatorInterface $translator
 */

$this->setTitle($translator->translate(App::FORBIDDEN));
?>
<div class="text-center">
    <h1>
        <?= $translator->translate(App::FORBIDDEN) ?>
    </h1>
    <p>
        <?= $translator->translate(App::THE_ACTION_FAILED_DUE_TO_INSUFFICIENT_RIGHTS) ?>
    </p>
</div>
