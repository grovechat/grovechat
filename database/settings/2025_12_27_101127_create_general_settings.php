<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.baseUrl', 'app.grovechat.com');
        $this->migrator->add('general.name', 'GroveChat');
        $this->migrator->add('general.logo', '');
        $this->migrator->add('general.copyright', 'Copyright © 2026 GroveChat');
        $this->migrator->add('general.icpRecord', '');
        $this->migrator->add('general.version', '0.0.1');
    }

    public function down(): void
    {
        $this->migrator->delete('general.baseUrl');
        $this->migrator->delete('general.name');
        $this->migrator->delete('general.logo');
        $this->migrator->delete('general.copyright');
        $this->migrator->delete('general.icpRecord');
        $this->migrator->delete('general.version');
    }
};
