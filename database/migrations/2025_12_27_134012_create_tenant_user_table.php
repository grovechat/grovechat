<?php

use App\Enums\TenantRole;
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
        Schema::create('tenant_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('tenant_id')->comment('租户ID');
            $table->integer('user_id')->comment('用户ID');
            $table->string('role')->default(TenantRole::CUSTOMER_SERVICE->value)->comment('角色');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_user');
    }
};
