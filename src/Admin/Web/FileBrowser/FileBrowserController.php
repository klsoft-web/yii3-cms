<?php

namespace App\Admin\Web\FileBrowser;

use App\Admin\Data\FileBrowser\FileBrowserRepositoryInterface;
use App\Messages\App;
use Exception;
use Klsoft\Yii3Authz\Permission;
use App\Data\Rbac\Permission as RbacPermission;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use SplFileInfo;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Status;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final class FileBrowserController
{
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
        private readonly CurrentUser                    $currentUser,
        private readonly TranslatorInterface            $translator,
        private readonly FormHydrator                   $formHydrator,
        private readonly ResponseFactoryInterface       $responseFactory,
        private readonly WebViewRenderer                $viewRenderer
    )
    {
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
        return $this->viewRenderer->renderPartial(
            __DIR__ . '/browser_template',
            [
                'canUserUpload' => $type === 'image' ? $this->currentUser->can(RbacPermission::UPLOAD_IMAGE) : $this->currentUser->can(RbacPermission::UPLOAD_FILE),
                'uploadDir' => $uploadDir,
                'files' => $this->getFiles($uploadDir)
            ]
        );
    }

    #[Permission(RbacPermission::CREATE_PAGE . '|' . RbacPermission::UPDATE_PAGE . '|' . RbacPermission::CREATE_POST . '|' . RbacPermission::UPDATE_POST)]
    public function files(ServerRequestInterface $request): ResponseInterface
    {
        $files = [];
        $form = new FilesForm();
        if (
            $this->formHydrator->populateFromPostAndValidate($form, $request) &&
            $this->isDirectoryAllowed($form->directory)
        ) {
            $files = $this->getFiles($form->directory);
        }
        return $this->viewRenderer->renderPartial(__DIR__ . '/browser_files_template', ['files' => $files]);
    }

    #[Permission(RbacPermission::UPLOAD_IMAGE . '|' . RbacPermission::UPLOAD_FILE)]
    public function upload(ServerRequestInterface $request): ResponseInterface
    {
        $form = new FilesForm();
        $errorMessage = '';
        if (
            $this->formHydrator->populateFromPostAndValidate($form, $request) &&
            $this->isDirectoryAllowed($form->directory)
        ) {
            $uploadedFiles = $request->getUploadedFiles();
            /** @var UploadedFileInterface|null $uploadedFile */
            $uploadedFile = $uploadedFiles['upload'] ?? null;
            if ($uploadedFile !== null) {
                $fileName = $uploadedFile->getClientFilename();
                if ($fileName) {
                    if ($this->isFileExtensionAllowed($fileName, $this->getDirectoryType($form->directory))) {
                        $targetFile = $this->fileBrowserRepository->getUploadPath() . $form->directory . $fileName;
                        if (!file_exists($targetFile)) {
                            try {
                                $uploadedFile->moveTo($targetFile);
                                $response = $this->responseFactory->createResponse(Status::OK);
                                $response->getBody()->write($form->directory . $fileName);
                                return $response;
                            } catch (Exception $e) {
                                $errorMessage = $e->getMessage();
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
        if (
            $this->formHydrator->populateFromPostAndValidate($form, $request) &&
            $this->isDirectoryAllowed($form->directory)
        ) {
            $newDir = $form->directory . $form->folder_name . '/';
            $dir = $this->fileBrowserRepository->getUploadPath() . $newDir;
            if (mkdir($dir)) {
                return $this->responseFactory->createResponse(Status::OK);
            }
        }

        return $this->responseFactory->createResponse(Status::INTERNAL_SERVER_ERROR);
    }

    private function isDirectoryAllowed(string $directory): bool
    {
        return str_starts_with($directory, '/' . $this->fileBrowserRepository->getUploadImagesDir()) ||
            str_starts_with($directory, '/' . $this->fileBrowserRepository->getUploadFilesDir());
    }

    private function getDirectoryType(string $directory): string
    {
        if (str_starts_with($directory, '/' . $this->fileBrowserRepository->getUploadImagesDir())) {
            return 'image';
        }
        return 'file';
    }

    private function getFiles(string $directory): array
    {
        $files = [];
        $dir = $this->fileBrowserRepository->getUploadPath() . $directory;
        $dirItems = scandir($dir);
        if (is_array($dirItems)) {
            foreach ($dirItems as $item) {
                if (!in_array($item, ['.', '..'])) {
                    if (is_dir($dir . $item)) {
                        $files[] = ['name' => $item, 'url' => $directory . $item . '/', 'type' => 'directory'];
                    } else {
                        $files[] = ['name' => $item, 'url' => $directory . $item, 'type' => $this->getFileType($item)];
                    }
                }
            }
        }

        return $files;
    }

    private function getUploadDir(string $type): string
    {
        if ($type === 'image') {
            return '/' . $this->fileBrowserRepository->getUploadImagesDir() . '/';
        } else {
            return '/' . $this->fileBrowserRepository->getUploadFilesDir() . '/';
        }
    }

    private function getFileType(string $fileName): string
    {
        if (in_array($this->getExtension($fileName), $this->imageAllowedExtensions)) {
            return 'image';
        }
        return 'file';
    }

    private function isFileExtensionAllowed(string $fileName, string $type): bool
    {
        if ($type == 'image') {
            return in_array($this->getExtension($fileName), $this->imageAllowedExtensions);
        } else {
            return in_array($this->getExtension($fileName), $this->fileAllowedExtensions);
        }
    }

    private function getExtension(string $fileName): string
    {
        $info = new SplFileInfo($fileName);
        return strtolower($info->getExtension());
    }
}
