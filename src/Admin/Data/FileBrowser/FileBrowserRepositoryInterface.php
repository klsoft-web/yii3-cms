<?php

namespace App\Admin\Data\FileBrowser;

interface FileBrowserRepositoryInterface
{
    public function getUploadImagesDir(): string;
    public function getUploadFilesDir(): string;
    public function getUploadPath(): string;
}
