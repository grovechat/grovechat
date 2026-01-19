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
        Schema::table('user_workspace', function (Blueprint $table) {
            $table->integer('online_status')->default(UserOnlineStatus::ONLINE->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_workspace', function (Blueprint $table) {
            $table->dropColumn('online_status');
        });
    }
};
