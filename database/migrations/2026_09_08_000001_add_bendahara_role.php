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
        // Pastikan role bendahara terdaftar di tabel roles
        $exists = DB::table('roles')->where('name', 'bendahara')->exists();
        if (!$exists) {
            DB::table('roles')->insert([
                'name' => 'bendahara',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('name', 'bendahara')->delete();
    }
};
