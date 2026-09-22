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
        // 1. Pastikan role petugas terdaftar di tabel roles
        $petugasRole = DB::table('roles')->where('name', 'petugas')->first();
        if (!$petugasRole) {
            $petugasRoleId = DB::table('roles')->insertGetId([
                'name' => 'petugas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $petugasRoleId = $petugasRole->id;
        }

        // 2. Pastikan role admin dan bendahara juga terdaftar jika belum ada
        if (!DB::table('roles')->where('name', 'admin')->exists()) {
            DB::table('roles')->insert([
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!DB::table('roles')->where('name', 'bendahara')->exists()) {
            DB::table('roles')->insert([
                'name' => 'bendahara',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Kaitkan user dengan email/nama petugas atau operator jika belum memiliki role
        DB::table('users')
            ->whereNull('role_id')
            ->where(function ($query) {
                $query->where('email', 'like', '%petugas%')
                    ->orWhere('email', 'like', '%operator%')
                    ->orWhere('name', 'like', '%Petugas%')
                    ->orWhere('name', 'like', '%Operator%');
            })
            ->update(['role_id' => $petugasRoleId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tetap biarkan data role agar tidak merusak relasi user
    }
};
