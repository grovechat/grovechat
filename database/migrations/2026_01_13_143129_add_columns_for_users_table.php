<?php

use App\Enums\UserOnlineStatus;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->string('nickname')->nullable();
            $table->integer('online_status')->default(UserOnlineStatus::ONLINE->value)->comment('在线状态');
            $table->timestamp('last_active_at')->nullable()->comment('最后活跃时间');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('nickname');
            $table->dropColumn('online_status');
            $table->dropColumn('last_active_at');
        });
    }
};
