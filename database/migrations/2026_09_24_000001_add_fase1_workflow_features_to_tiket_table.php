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
        // 1. Update tiket table
        Schema::table('tiket', function (Blueprint $table) {
            $table->string('status', 30)->default('OPEN')->change();
            $table->foreignId('resolved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->dateTime('resolved_at')->nullable()->after('tanggal_close')->index();
            $table->text('closing_notes_teknisi')->nullable()->after('deskripsi');
            $table->string('tipe_penanganan', 50)->default('JOINTING_LURUS')->after('closing_notes_teknisi');
            $table->dateTime('first_response_at')->nullable()->after('tanggal_open');
            $table->integer('response_time_minutes')->nullable()->after('first_response_at');
            $table->integer('total_stop_clock_minutes')->default(0)->after('mttr_minutes');
            $table->boolean('is_stop_clock')->default(false)->after('total_stop_clock_minutes')->index();
        });

        // 2. Create tiket_stop_clocks table
        Schema::create('tiket_stop_clocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tiket')->constrained('tiket')->onDelete('cascade');
            $table->dateTime('start_time')->index();
            $table->dateTime('end_time')->nullable()->index();
            $table->integer('duration_minutes')->default(0);
            $table->string('alasan_kategori', 100);
            $table->text('alasan_detail')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('stopped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        // 3. Create tiket_handover_shifts table
        Schema::create('tiket_handover_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tiket')->constrained('tiket')->onDelete('cascade');
            $table->string('shift_from', 50);
            $table->string('shift_to', 50);
            $table->foreignId('user_from_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_handover');
            $table->timestamps();
        });

        // 4. Update resume_pekerjaan table
        Schema::table('resume_pekerjaan', function (Blueprint $table) {
            $table->string('tipe_penanganan', 50)->nullable()->after('id_tiket');
            $table->string('joint_closure_type', 100)->nullable()->after('tipe_penanganan');
            $table->integer('core_count_jointed')->nullable()->after('joint_closure_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_pekerjaan', function (Blueprint $table) {
            $table->dropColumn(['tipe_penanganan', 'joint_closure_type', 'core_count_jointed']);
        });

        Schema::dropIfExists('tiket_handover_shifts');
        Schema::dropIfExists('tiket_stop_clocks');

        Schema::table('tiket', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn([
                'resolved_by',
                'resolved_at',
                'closing_notes_teknisi',
                'tipe_penanganan',
                'first_response_at',
                'response_time_minutes',
                'total_stop_clock_minutes',
                'is_stop_clock',
            ]);
        });
    }
};
