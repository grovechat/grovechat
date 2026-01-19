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
        Schema::create('tags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();
            $table->string('name')->comment('标签名');
            $table->string('color')->comment('标签颜色');
            $table->string('description')->comment('标签描述');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
