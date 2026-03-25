<?php

declare(strict_types=1);

namespace App\Admin\Web\Shared\Layout\Main;

use Yiisoft\Assets\AssetBundle;

final class CkeditorAsset extends AssetBundle
{
    public ?string $basePath = '@assets/admin';
    public ?string $baseUrl = '@assetsUrl/admin';
    public ?string $sourcePath = '@npmAssetSource/ckeditor';

    public array $js = [
        'ckeditor.js',
    ];
}
