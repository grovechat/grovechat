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
        Schema::connection('sqlite')->create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->comment('工作区名');
            $table->string('slug')->nullable()->comment('工作区标识');
            $table->string('logo')->nullable()->comment('工作区logo');
            $table->string('path')->comment('工作区url访问路径');
            $table->integer('owner_id')->nullable()->comment('所有者ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sqlite')->dropIfExists('workspaces');
    }
};
