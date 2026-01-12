<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_profiles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();

            $table->string('name');
            $table->string('provider'); // \App\Enums\StorageProvider value

            $table->string('key')->nullable();
            $table->text('secret')->nullable();

            $table->string('bucket')->nullable();
            $table->string('region')->nullable();
            $table->string('endpoint')->nullable();
            $table->string('url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_profiles');
    }
};

