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
        if (Schema::hasTable('slides') && !Schema::hasColumn('slides', 'gambar_base64')) {
            Schema::table('slides', function (Blueprint $table) {
                $table->longText('gambar_base64')->nullable()->after('gambar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('slides') && Schema::hasColumn('slides', 'gambar_base64')) {
            Schema::table('slides', function (Blueprint $table) {
                $table->dropColumn('gambar_base64');
            });
        }
    }
};
