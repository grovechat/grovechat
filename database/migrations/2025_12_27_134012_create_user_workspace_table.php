<?php

use App\Enums\WorkspaceRole;
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
        Schema::create('user_workspace', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('workspace_id')->comment('工作区ID');
            $table->integer('user_id')->comment('用户ID');
            $table->string('role')->default(WorkspaceRole::CUSTOMER_SERVICE->value)->comment('角色');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_workspace');
    }
};
