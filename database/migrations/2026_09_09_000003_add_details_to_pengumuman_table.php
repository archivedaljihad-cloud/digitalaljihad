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
        if (Schema::hasTable('pengumuman')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                if (!Schema::hasColumn('pengumuman', 'judul')) {
                    $table->string('judul')->nullable()->after('id');
                }
                if (!Schema::hasColumn('pengumuman', 'pemateri')) {
                    $table->string('pemateri')->nullable()->after('judul');
                }
                if (!Schema::hasColumn('pengumuman', 'foto')) {
                    $table->string('foto')->nullable()->after('pemateri');
                }
                if (!Schema::hasColumn('pengumuman', 'waktu')) {
                    $table->string('waktu')->nullable()->after('tanggal');
                }
                if (!Schema::hasColumn('pengumuman', 'tempat')) {
                    $table->string('tempat')->nullable()->after('waktu');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengumuman')) {
            Schema::table('pengumuman', function (Blueprint $table) {
                $columns = ['judul', 'pemateri', 'foto', 'waktu', 'tempat'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('pengumuman', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
