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
        Schema::connection('sqlite')->create('tenants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->comment('租户名');
            $table->string('slug')->comment('租户标识');
            $table->string('logo')->nullable()->comment('租户logo');
            $table->string('path')->comment('租户url访问路径');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlite')->dropIfExists('tenants');
    }
};
