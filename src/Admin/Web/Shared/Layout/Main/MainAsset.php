<?php

declare(strict_types=1);

namespace App\Admin\Web\Shared\Layout\Main;

use App\Web\Shared\Layout\Main\BootstrapAsset;
use App\Web\Shared\Layout\Main\BootstrapIconsAsset;
use Yiisoft\Assets\AssetBundle;

final class MainAsset extends AssetBundle
{
    public ?string $basePath = '@assets/admin';
    public ?string $baseUrl = '@assetsUrl/admin';
    public ?string $sourcePath = '@assetsSource/admin';

    public array $css = [
        'admin.css',
    ];

    public array $depends = [
        BootstrapAsset::class,
        BootstrapIconsAsset::class,
    ];
}
