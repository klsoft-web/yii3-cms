<?php

declare(strict_types=1);

namespace App\Admin\Web\Shared\Layout\Main;

use Yiisoft\Assets\AssetBundle;

final class EditAsset extends AssetBundle
{
    public ?string $basePath = '@assets/admin';
    public ?string $baseUrl = '@assetsUrl/admin';
    public ?string $sourcePath = '@assetsSource/admin';

    public array $css = [
        'edit.css',
    ];

    public array $js = [
        'edit.js',
    ];

    public array $depends = [
        MainAsset::class,
        CkeditorAsset::class
    ];
}
