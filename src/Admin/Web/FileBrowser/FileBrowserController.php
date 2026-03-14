<?php

namespace App\Admin\Web\FileBrowser;

use App\Admin\Data\FileBrowser\FileBrowserRepositoryInterface;
use App\Messages\App;
use Klsoft\Yii3Authz\Permission;
use App\Data\Rbac\Permission as RbacPermission;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SplFileInfo;
use Yiisoft\Aliases\Aliases;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Status;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class FileBrowserController
{
    private string $serverUploadDir;
    private array $imageAllowedExtensions = [
        'bmp',
        'gif',
        'jpeg',
        'jpg',
        'png',
        'webp'
    ];
    private array $fileAllowedExtensions = [
        '7z',
        'csv',
        'doc',
        'docx',
        'mp3',
        'mp4',
        'odt',
        'pdf',
        'ppt',
        'pptx',
        'rar',
        'tar',
        'tgz',
        'txt',
        'wav',
        'xls',
        'xlsx',
        'xml',
        'zip'
    ];

    public function __construct(
        private readonly FileBrowserRepositoryInterface $fileBrowserRepository,
        private readonly Aliases                        $aliases,
        private readonly CurrentUser                    $currentUser,
        private readonly TranslatorInterface            $translator,
        private readonly FormHydrator                   $formHydrator,
        private readonly ResponseFactoryInterface       $responseFactory,
        private readonly WebViewRenderer                $viewRenderer)
    {
        $this->serverUploadDir = $this->aliases->get('@public');
    }

    #[Permission(
        RbacPermission::CREATE_POST . '|' .
        RbacPermission::UPDATE_POST . '|' .
        RbacPermission::CREATE_PAGE . '|' .
        RbacPermission::UPDATE_PAGE . '|' .
        RbacPermission::CREATE_CATEGORY . '|' .
        RbacPermission::UPDATE_CATEGORY
    )]
    public function browser(#[RouteArgument('type')] string $type): ResponseInterface
    {
        $uploadDir = $this->getUploadDir($type);
        return $this->viewRenderer->renderPartial(__DIR__ . '/browser',
            [
                'canUserUpload' => $type === 'image' ? $this->currentUser->can(RbacPermission::UPLOAD_IMAGE) : $this->currentUser->can(RbacPermission::UPLOAD_FILE),
                'uploadDir' => $uploadDir,
                'files' => $this->getFiles($uploadDir)]);
    }

    #[Permission(RbacPermission::CREATE_PAGE . '|' . RbacPermission::UPDATE_PAGE . '|' . RbacPermission::CREATE_POST . '|' . RbacPermission::UPDATE_POST)]
    public function files(ServerRequestInterface $request): ResponseInterface
    {
        $files = [];
        $form = new FilesForm();
        if ($this->formHydrator->populateFromPostAndValidate($form, $request) &&
            $this->isDirectoryAllowed($form->directory)) {
            $files = $this->getFiles($form->directory);
        }
        return $this->viewRenderer->renderPartial(__DIR__ . '/browser-files', ['files' => $files]);
    }

    #[Permission(RbacPermission::UPLOAD_IMAGE . '|' . RbacPermission::UPLOAD_FILE)]
    public function upload(ServerRequestInterface $request): ResponseInterface
    {

        $form = new FilesForm();
        $errorMessage = '';
        if ($this->formHydrator->populateFromPostAndValidate($form, $request) &&
            $this->isDirectoryAllowed($form->directory)) {
            $fileName = basename($_FILES['upload']['name']);
            if ($this->isFileExtensionAllowed($fileName, $this->getDirectoryType($form->directory))) {
                $target_file = $this->serverUploadDir . $form->directory . $fileName;
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES['upload']['tmp_name'], $target_file)) {
                        $response = $this->responseFactory->createResponse(Status::OK);
                        $response->getBody()->write($form->directory . $fileName);
                        return $response;
                    } else {
                        switch ($_FILES['upload']['error']) {
                            case UPLOAD_ERR_INI_SIZE:
                                $errorMessage = $this->translator->translate(App::THE_UPLOADED_FILE_EXCEEDS_THE_UPLOAD_MAX_FILE_SIZE, ['upload_max_filesize' => ini_get('upload_max_filesize')]);
                                break;
                            case UPLOAD_ERR_CANT_WRITE:
                                $errorMessage = $this->translator->translate(App::FAILED_TO_WRITE_FILE_TO_DISK);
                                break;
                        }
                    }
                } else {
                    $response = $this->responseFactory->createResponse(Status::FORBIDDEN);
                    $response->getBody()->write($this->translator->translate(App::THE_FILE_ALREADY_EXISTS));
                    return $response;
                }
            } else {
                $response = $this->responseFactory->createResponse(Status::FORBIDDEN);
                $response->getBody()->write($this->translator->translate(App::THIS_FiLE_EXTENSION_IS_NOT_ALLOWED, ['file_extension' => $this->getExtension($fileName)]));
                return $response;
            }
        }

        $response = $this->responseFactory->createResponse(Status::INTERNAL_SERVER_ERROR);
        $response->getBody()->write($errorMessage);
        return $response;
    }

    #[Permission(RbacPermission::UPLOAD_IMAGE . '|' . RbacPermission::UPLOAD_FILE)]
    public function createFolder(ServerRequestInterface $request): ResponseInterface
    {
        $form = new CreateFolderForm();
        if ($this->formHydrator->populateFromPostAndValidate($form, $request) &&
            $this->isDirectoryAllowed($form->directory)) {
            $newDir = $form->directory . $form->folder_name . '/';
            $dir = $this->serverUploadDir . $newDir;
            if (mkdir($dir)) {
                return $this->responseFactory->createResponse(Status::OK);
            }
        }

        return $this->responseFactory->createResponse(Status::INTERNAL_SERVER_ERROR);
    }

    private function isDirectoryAllowed($directory): bool
    {
        return str_starts_with($directory, '/' . $this->fileBrowserRepository->getUploadImagesDir()) ||
            str_starts_with($directory, '/' . $this->fileBrowserRepository->getUploadFilesDir());
    }

    private function getDirectoryType($directory): string
    {
        if (str_starts_with($directory, '/' . $this->fileBrowserRepository->getUploadImagesDir())) {
            return 'image';
        }
        return 'file';
    }

    private function getFiles($directory): array
    {
        $files = [];
        $dir = $this->serverUploadDir . $directory;
        $dirItems = scandir($dir);
        foreach ($dirItems as $item) {
            if (!in_array($item, ['.', '..'])) {
                if (is_dir($dir . $item)) {
                    $files[] = ['name' => $item, 'url' => $directory . $item . '/', 'type' => 'directory'];
                } else {
                    $files[] = ['name' => $item, 'url' => $directory . $item, 'type' => $this->getFileType($item)];
                }
            }
        }

        return $files;
    }

    private function getUploadDir($type): string
    {
        if ($type === 'image') {
            return '/' . $this->fileBrowserRepository->getUploadImagesDir() . '/';
        } else {
            return '/' . $this->fileBrowserRepository->getUploadFilesDir() . '/';
        }
    }

    private function getFileType($fileName): string
    {
        if (in_array($this->getExtension($fileName), $this->imageAllowedExtensions)) {
            return 'image';
        }
        return 'file';
    }

    private function isFileExtensionAllowed($fileName, $type): bool
    {
        if ($type == 'image') {
            return in_array($this->getExtension($fileName), $this->imageAllowedExtensions);
        } else {
            return in_array($this->getExtension($fileName), $this->fileAllowedExtensions);
        }
    }

    private function getExtension($fileName): string
    {
        $info = new SplFileInfo($fileName);
        return strtolower($info->getExtension());
    }
}
