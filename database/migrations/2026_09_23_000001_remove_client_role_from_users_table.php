<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus akun pengguna dengan role client jika ada
        DB::table('users')->where('role', 'client')->delete();

        // Ubah enum kolom role pada tabel users tanpa client (khusus MySQL / MariaDB)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'helpdesk', 'teknis', 'sa_cs') NOT NULL DEFAULT 'teknis'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'helpdesk', 'teknis', 'sa_cs', 'client') NOT NULL DEFAULT 'teknis'");
        }
    }
};
