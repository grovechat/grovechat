<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('storage.enabled', false);
        $this->migrator->add('storage.provider', false);
        $this->migrator->add('storage.key', null);
        $this->migrator->addEncrypted('storage.secret', null);
        
        $this->migrator->add('storage.bucket', null);
        $this->migrator->add('storage.region', 'us-east-1');
        $this->migrator->add('storage.endpoint', null);
        
        $this->migrator->add('storage.url');
    }
    
    public function down(): void
    {
        $this->migrator->delete('storage.enabled');
        $this->migrator->delete('storage.provider');
        $this->migrator->delete('storage.key');
        $this->migrator->delete('storage.secret');
        $this->migrator->delete('storage.bucket');
        $this->migrator->delete('storage.region');
        $this->migrator->delete('storage.endpoint');
    }
};
