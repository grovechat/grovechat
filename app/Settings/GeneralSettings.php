<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $baseUrl; // 主机地址

    public ?string $name;  // 系统名称

    public ?string $logoId;  // 系统logo

    public ?string $copyright; // 版权信息

    public ?string $icpRecord; // 备案信息

    public ?string $version; // 版本号

    public static function group(): string
    {
        return 'general';
    }
}
