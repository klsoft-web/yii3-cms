<?php

namespace App\Admin\Data\FileBrowser;

final readonly class FileBrowserRepository implements FileBrowserRepositoryInterface
{
    public function __construct(
        private string $uploadImagesDir,
        private string $uploadFilesDir)
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
}
