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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->default('')->comment('租户名');
            $table->string('slug')->default('')->comment('租户标识');
            $table->string('logo')->default('')->comment('租户logo');
            $table->string('path')->default('')->comment('租户路径');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
