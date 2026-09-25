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
        Schema::table('push_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('push_subscriptions', 'device_type')) {
                $table->string('device_type', 30)->default('desktop')->after('content_encoding');
            }
            if (!Schema::hasColumn('push_subscriptions', 'is_mobile')) {
                $table->boolean('is_mobile')->default(false)->after('device_type');
            }
            if (!Schema::hasColumn('push_subscriptions', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('is_mobile');
            }
            if (!Schema::hasColumn('push_subscriptions', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable()->after('user_agent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('push_subscriptions', 'device_type')) $columns[] = 'device_type';
            if (Schema::hasColumn('push_subscriptions', 'is_mobile')) $columns[] = 'is_mobile';
            if (Schema::hasColumn('push_subscriptions', 'user_agent')) $columns[] = 'user_agent';
            if (Schema::hasColumn('push_subscriptions', 'last_active_at')) $columns[] = 'last_active_at';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
