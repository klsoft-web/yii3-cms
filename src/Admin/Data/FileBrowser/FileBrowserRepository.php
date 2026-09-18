<?php

namespace App\Admin\Data\FileBrowser;

use Yiisoft\Aliases\Aliases;

final readonly class FileBrowserRepository implements FileBrowserRepositoryInterface
{
    public function __construct(
        private string  $uploadImagesDir,
        private string  $uploadFilesDir,
        private Aliases $aliases)
    {
    }

    public function getUploadImagesDir(): string
    {
        return $this->uploadImagesDir;
    }

    public function getUploadFilesDir(): string
    {
        return $this->uploadFilesDir;
    }

    public function getUploadPath(): string
    {
        return $this->aliases->get('@public');
    }
}
