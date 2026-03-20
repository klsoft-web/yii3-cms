<?php

declare(strict_types=1);

use Klsoft\Yii3CmsCore\Data\Nav\NavItemType;
use App\Domain\Site\SiteManagerInterface;
use App\Shared\ApplicationParams;
use App\Web\Shared\Layout\Main\MainAsset;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;

/**
 * @var WebView $this
 * @var ApplicationParams $applicationParams
 * @var Aliases $aliases
 * @var AssetManager $assetManager
 * @var CurrentRoute $currentRoute
 * @var UrlGeneratorInterface $urlGenerator
 * @var string $content
 * @var SiteManagerInterface $siteManager
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
        <div class="container">
            <a href="/" class="navbar-brand"><img src="<?= $aliases->get('@baseUrl/favicon.svg') ?>" alt=""></a>
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
                    <?php
                    foreach ($siteManager->getTopNavItems() as $navItem) {
                        $url = $navItem['nav_item_type'] === NavItemType::Slug ? '/' . $navItem['value'] : $navItem['value'];
                        echo Html::li(Html::a($navItem['name'], $url, ['class' => 'nav-link' . ($url === $currentRoute->getUri()->getPath() ? ' active' : '')]))->attributes(['class' => 'nav-item']);
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container">
    <?= $content ?>
</main>

<footer class="footer mt-auto py-3 bg-body-tertiary">
    <div class="container">
        <?php if (count($siteManager->getBottomNavs()) > 0): ?>
            <div class="row">
                <?php foreach ($siteManager->getBottomNavs() as $navName => $navItems): ?>
                    <div class="col-auto mx-3">
                        <h5><?= $navName ?></h5>
                        <ul class="list-unstyled text-small">
                            <?php foreach ($navItems as $navItem): ?>
                                <li>
                                    <a class="link-secondary text-decoration-none"
                                       href="<?= $navItem['nav_item_type'] === NavItemType::Slug ? '/' . $navItem['value'] : $navItem['value'] ?>"><?= $navItem['name'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
