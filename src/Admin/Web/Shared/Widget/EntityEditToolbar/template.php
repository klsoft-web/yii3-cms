<?php

declare(strict_types=1);

use App\Messages\App;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;

/**
 * @var UrlGeneratorInterface $urlGenerator
 * @var TranslatorInterface $translator
 * @var string $listOfEntityRouteName
 */
?>
<button class="btn btn-primary" type="submit"><?= $translator->translate(App::SAVE) ?></button>
<button id="cancel-btn" class="btn" type="button"
        data-cancel-url="<?= $urlGenerator->generate($listOfEntityRouteName) ?>"><?= $translator->translate(App::CANCEL) ?></button>
