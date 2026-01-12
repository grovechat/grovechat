<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $base_url; // 主机地址

    public ?string $name;  // 系统名称

    public ?string $logo_id;  // 系统logo

    public ?string $copyright; // 版权信息

    public ?string $icp_record; // 备案信息

    public ?string $version; // 版本号

    public static function group(): string
    {
        return 'general';
    }
}
