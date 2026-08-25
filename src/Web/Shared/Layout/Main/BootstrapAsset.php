<?php

namespace App\Web\Shared\Layout\Main;

use Yiisoft\Assets\AssetBundle;

final class BootstrapAsset extends AssetBundle
{
    public ?string $basePath = '@assets/main';
    public ?string $baseUrl = '@assetsUrl/main';
    public ?string $sourcePath = '@npmAssetSource/bootstrap/dist';

    public array $css = [
        'css/bootstrap.min.css'
    ];
    public array $js = [
        'js/bootstrap.bundle.min.js'
    ];
}
