<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.base_url', 'https://app.grovechat.com');
        $this->migrator->add('general.name', 'GroveChat');
        $this->migrator->add('general.logo_id', null);
        $this->migrator->add('general.copyright', 'Copyright © 2026 GroveChat');
        $this->migrator->add('general.icp_record', null);
        $this->migrator->add('general.version', '0.0.1');
    }

    public function down(): void
    {
        $this->migrator->delete('general.base_url');
        $this->migrator->delete('general.name');
        $this->migrator->delete('general.logo_id');
        $this->migrator->delete('general.copyright');
        $this->migrator->delete('general.icp_record');
        $this->migrator->delete('general.version');
    }
};
