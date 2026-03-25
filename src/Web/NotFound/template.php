<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\View\WebView;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\CurrentRoute;

/**
 * @var WebView $this
 * @var UrlGeneratorInterface $urlGenerator
 * @var CurrentRoute $currentRoute
 */

$this->setTitle('404');
?>

<div class="text-center">
    <h1>
        404
    </h1>

    <p>
        The page
        <strong><?= Html::encode($currentRoute->getUri()?->getPath() ?? 'unknown') ?></strong>
        not found.
    </p>

    <p>
        The above error occurred while the Web server was processing your request.<br/>
        Please contact us if you think this is a server error. Thank you.
    </p>

    <p>
        <a href="<?= $urlGenerator->generate('main') ?>">Go Back Home</a>
    </p>
</div>
