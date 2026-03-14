<?php

namespace App\Admin\Web\Shared\Layout\Main;

use Yiisoft\Assets\AssetBundle;

final class BootstrapAsset extends AssetBundle
{
    public ?string $basePath = '@assets/main';
    public ?string $baseUrl = '@assetsUrl/main';
    public ?string $sourcePath = '@assetsSource/main';

    public array $css = [
        'bootstrap.min.css',
        'bootstrap-icons.min.css'
    ];
    public array $js = [
        'bootstrap.bundle.min.js'
    ];
}
