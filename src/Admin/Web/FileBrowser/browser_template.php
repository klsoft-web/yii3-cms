<?php

declare(strict_types=1);

use App\Admin\Web\FileBrowser\CreateFolderForm;
use App\Admin\Web\Shared\Layout\Main\FileBrowserAsset;
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
 * @var bool $canUserUpload
 * @var string $uploadDir
 * @var array $files
 */

$assetManager->register(FileBrowserAsset::class);

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
    <title><?= $translator->translate(App::FILE_BROWSER) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <input id="upload-dir" type="hidden" value="<?= $uploadDir ?>"/>
    <input id="current-dir" type="hidden" value="<?= $uploadDir ?>"/>
    <input id="file" type="file" class="d-none"/>
    <div class="btn-group mt-3 ms-3<?= !$canUserUpload ? ' d-none' : '' ?>" role="group">
        <button id="upload-btn" type="button"
                class="btn btn-outline-primary"><?= $translator->translate(App::UPLOAD) ?></button>
        <button id="show-create-folder-dialog-btn" type="button"
                class="btn btn-outline-primary"><?= $translator->translate(App::CREATE_FOLDER) ?></button>
    </div>
    <ol id="breadcrumb" class="breadcrumb m-3 d-none"></ol>
</header>

<main id="content" class="container-fluid m-3">
    <?= $this->render(__DIR__ . '/browser_files_template', ['files' => $files]) ?>
</main>

<div id="create-folder-dialog" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $translator->translate(App::CREATE_FOLDER) ?></h5>
            </div>
            <div class="modal-body">
                <label for="folder-name" class="form-label"><?= $translator->translate(App::NAME) ?></label>
                <input id="folder-name" type="text" class="form-control" placeholder="my-folder"
                       maxlength="<?= CreateFolderForm::FOLDER_NAME_MAX_LENGTH ?>" required>
                <div id="folder-name-error" class="invalid-feedback">
                    <?= $translator->translate(App::FIELD_NAME_IS_INVALID, ['field_name' => $translator->translate(App::NAME)]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn"
                        data-bs-dismiss="modal"><?= $translator->translate(App::CLOSE) ?></button>
                <button id="create-folder-btn" type="button" class="btn btn-primary">
                    <span id="create-folder-btn-spinner" class="spinner-border spinner-border-sm d-none"
                          aria-hidden="true"></span>
                    <span><?= $translator->translate(App::CREATE) ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<?= Html::hiddenInput('csrf', $csrf, ['id' => 'csrf']) ?>
<?= Html::hiddenInput('files-url', $urlGenerator->generate('admin/file-browser/files'), ['id' => 'files-url']) ?>
<?= Html::hiddenInput('create-folder-url', $urlGenerator->generate('admin/file-browser/create-folder'), ['id' => 'create-folder-url']) ?>
<?= Html::hiddenInput('upload-url', $urlGenerator->generate('admin/file-browser/upload'), ['id' => 'upload-url']) ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
