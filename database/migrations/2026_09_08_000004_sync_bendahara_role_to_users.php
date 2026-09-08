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

        // Sinkronkan semua akun bendahara ke role_id bendahara
        DB::table('users')
            ->where('email', 'like', '%bendahara%')
            ->orWhere('name', 'like', '%bendahara%')
            ->update(['role_id' => $roleId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to avoid breaking foreign keys
    }
};
