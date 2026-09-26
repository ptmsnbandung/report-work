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
        Schema::table('kronologis', function (Blueprint $table) {
            if (!Schema::hasColumn('kronologis', 'video_url')) {
                $table->string('video_url', 255)->nullable()->after('foto_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kronologis', function (Blueprint $table) {
            if (Schema::hasColumn('kronologis', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
