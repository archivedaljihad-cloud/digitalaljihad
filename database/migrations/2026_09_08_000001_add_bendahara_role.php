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
        $role = DB::table('roles')->where('name', 'bendahara')->first();
        if (!$role) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'bendahara',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $roleId = $role->id;
        }

        // Pastikan user bendahara diarahkan ke role bendahara
        DB::table('users')
            ->where('email', 'bendahara@masjid.com')
            ->orWhere('email', 'bendahara@aljihad.com')
            ->orWhere('name', 'like', '%Bendahara%')
            ->update(['role_id' => $roleId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('name', 'bendahara')->delete();
    }
};
