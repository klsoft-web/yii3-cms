<?php

declare(strict_types=1);

use App\Admin\Web\Shared\Layout\Main\MainAsset;
use App\Messages\App;
use App\Shared\ApplicationParams;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Renderer\Csrf;

/**
 * @var WebView $this
 * @var Csrf $csrf
 * @var AssetManager $assetManager
 * @var CurrentRoute $currentRoute
 * @var ApplicationParams $applicationParams
 * @var Aliases $aliases
 * @var UrlGeneratorInterface $urlGenerator
 * @var TranslatorInterface $translator
 * @var string $content
 */

$assetManager->register(MainAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addCssStrings($assetManager->getCssStrings());
$this->addJsFiles($assetManager->getJsFiles());
$this->addJsStrings($assetManager->getJsStrings());
$this->addJsVars($assetManager->getJsVars());

$this->beginPage()
?>
<!DOCTYPE html>
<html class="h-100" lang="<?= Html::encode($applicationParams->locale) ?>">
<head>
    <meta charset="<?= Html::encode($applicationParams->charset) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= $aliases->get('@baseUrl/favicon.svg') ?>" type="image/svg+xml">
    <title><?= Html::encode($this->getTitle()) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <nav class="navbar navbar-expand-md navbar-dark navbar-bg mb-4">
        <div class="container-fluid">
            <a href="/" class="navbar-brand">Yii3-CMS</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse"
                aria-controls="navbarCollapse"
                aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                </ul>
                <?= Html::form()
                    ->post($urlGenerator->generate('logout'))
                    ->csrf($csrf)
                    ->open() ?>
                <button class="btn btn-outline-light" type="submit">
                    <?= $translator->translate(App::SIGN_OUT) ?>
                </button>
                <?= Html::form()->close() ?>
            </div>
        </div>
    </nav>
</header>

<main class="container-fluid">
    <div class="row">
        <div class="col-sm-2 mb-3">
            <ul class="list-group">
                <?= Html::a($translator->translate(App::POSTS), $urlGenerator->generate('admin/posts'), ['class' => 'list-group-item list-group-item-action' . ($currentRoute->getName() === 'admin/posts' || $currentRoute->getName() === 'admin/create-post' || $currentRoute->getName() === 'admin/update-post' ? ' active' : '')]) ?>
                <?= Html::a($translator->translate(App::PAGES), $urlGenerator->generate('admin/pages'), ['class' => 'list-group-item list-group-item-action' . ($currentRoute->getName() === 'admin/pages' || $currentRoute->getName() === 'admin/create-page' || $currentRoute->getName() === 'admin/update-page' ? ' active' : '')]) ?>
                <?= Html::a($translator->translate(App::CATEGORIES), $urlGenerator->generate('admin/categories'), ['class' => 'list-group-item list-group-item-action' . ($currentRoute->getName() === 'admin/categories' || $currentRoute->getName() === 'admin/create-category' || $currentRoute->getName() === 'admin/update-category' ? ' active' : '')]) ?>
                <?= Html::a($translator->translate(App::NAVIGATIONS), $urlGenerator->generate('admin/navs'), ['class' => 'list-group-item list-group-item-action' . ($currentRoute->getName() === 'admin/navs' || $currentRoute->getName() === 'admin/create-nav' || $currentRoute->getName() === 'admin/update-nav' ? ' active' : '')]) ?>
                <?= Html::a($translator->translate(App::USERS), $urlGenerator->generate('admin/users'), ['class' => 'list-group-item list-group-item-action' . ($currentRoute->getName() === 'admin/users' || $currentRoute->getName() === 'admin/create-user' || $currentRoute->getName() === 'admin/update-user' ? ' active' : '')]) ?>
                <?= Html::a($translator->translate(App::ROLES), $urlGenerator->generate('admin/roles'), ['class' => 'list-group-item list-group-item-action' . ($currentRoute->getName() === 'admin/roles' || $currentRoute->getName() === 'admin/create-role' || $currentRoute->getName() === 'admin/update-role' ? ' active' : '')]) ?>
                <?= Html::a($translator->translate(App::LOG), $urlGenerator->generate('admin/log'), ['class' => 'list-group-item list-group-item-action' . ($currentRoute->getName() === 'admin/log' || $currentRoute->getName() === 'admin/log' ? ' active' : '')]) ?>
            </ul>
        </div>
        <div class="col-sm-10"><?= $content ?></div>
    </div>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
