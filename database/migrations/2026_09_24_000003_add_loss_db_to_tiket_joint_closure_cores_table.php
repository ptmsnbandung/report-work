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
        if (Schema::hasTable('tiket_joint_closure_cores') && !Schema::hasColumn('tiket_joint_closure_cores', 'loss_db')) {
            Schema::table('tiket_joint_closure_cores', function (Blueprint $table) {
                $table->decimal('loss_db', 5, 2)->nullable()->after('status_core')->comment('Redaman sambungan dB');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tiket_joint_closure_cores') && Schema::hasColumn('tiket_joint_closure_cores', 'loss_db')) {
            Schema::table('tiket_joint_closure_cores', function (Blueprint $table) {
                $table->dropColumn('loss_db');
            });
        }
    }
};
