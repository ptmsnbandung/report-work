<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan role 'manager_teknisi' ke enum users.role
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'helpdesk', 'teknis', 'sa_cs', 'manager_teknisi') NOT NULL DEFAULT 'teknis'");
        }

        // 2. Tambahkan kolom penugasan pada tabel tiket
        Schema::table('tiket', function (Blueprint $table) {
            $table->foreignId('assigned_lead_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->json('assigned_team')->nullable()->after('assigned_lead_id');
            $table->foreignId('assigned_by')->nullable()->after('assigned_team')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_by');
            $table->text('catatan_dispatch')->nullable()->after('assigned_at');
            $table->string('dispatch_status', 20)->default('unassigned')->after('catatan_dispatch')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropForeign(['assigned_lead_id']);
            $table->dropForeign(['assigned_by']);
            $table->dropColumn([
                'assigned_lead_id',
                'assigned_team',
                'assigned_by',
                'assigned_at',
                'catatan_dispatch',
                'dispatch_status',
            ]);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'helpdesk', 'teknis', 'sa_cs') NOT NULL DEFAULT 'teknis'");
        }
    }
};
