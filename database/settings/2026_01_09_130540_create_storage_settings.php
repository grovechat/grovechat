<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('storage.enabled', false);
        $this->migrator->add('storage.disk', false);
        $this->migrator->add('storage.key', null);
        $this->migrator->addEncrypted('storage.secret', null);
        
        $this->migrator->add('storage.bucket', null);
        $this->migrator->add('storage.region', 'us-east-1');
        $this->migrator->add('storage.endpoint', null);
        
        $this->migrator->add('storage.url');
        $this->migrator->add('storage.pathStyle', false);
    }
    
    public function down(): void
    {
        $this->migrator->delete('storage.enabled');
        $this->migrator->delete('storage.disk');
        $this->migrator->delete('storage.key');
        $this->migrator->delete('storage.secret');
        $this->migrator->delete('storage.bucket');
        $this->migrator->delete('storage.region');
        $this->migrator->delete('storage.endpoint');
        $this->migrator->delete('storage.pathStyle');
    }
};
