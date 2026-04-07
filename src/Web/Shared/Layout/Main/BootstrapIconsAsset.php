<?php

namespace App\Web\Shared\Layout\Main;

use Yiisoft\Assets\AssetBundle;

final class BootstrapIconsAsset extends AssetBundle
{
    public ?string $basePath = '@assets/main';
    public ?string $baseUrl = '@assetsUrl/main';
    public ?string $sourcePath = '@npmAssetSource/bootstrap-icons/font';

    public array $css = [
        'bootstrap-icons.min.css'
    ];
}
