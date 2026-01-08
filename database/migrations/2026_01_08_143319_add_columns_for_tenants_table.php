<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('sqlite')->table('tenants', function (Blueprint $table) {
            $table->integer('owner_id')->nullable()->comment('所有者ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlite')->table('tenants', function (Blueprint $table) {
            $table->dropIndex('owner_id');
        });
    }
};
