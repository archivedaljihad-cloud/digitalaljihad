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
        // Pastikan role bendahara ada
        $bendaharaRole = DB::table('roles')->where('name', 'bendahara')->first();
        if (!$bendaharaRole) {
            $bendaharaId = DB::table('roles')->insertGetId([
                'name' => 'bendahara',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $bendaharaId = $bendaharaRole->id;
        }

        // Pastikan role petugas ada
        $petugasRole = DB::table('roles')->where('name', 'petugas')->first();
        if (!$petugasRole) {
            $petugasId = DB::table('roles')->insertGetId([
                'name' => 'petugas',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $petugasId = $petugasRole->id;
        }

        // Koreksi user bendahara agar pasti terhubung ke role bendahara
        DB::table('users')
            ->where(function ($q) {
                $q->where('email', 'like', '%bendahara%')
                  ->orWhere('name', 'like', '%Bendahara%');
            })
            ->update(['role_id' => $bendaharaId]);

        // Koreksi user dkm/operator agar pasti terhubung ke role petugas
        DB::table('users')
            ->where(function ($q) {
                $q->where('email', 'like', '%petugas%')
                  ->orWhere('email', 'like', '%operator%')
                  ->orWhere('email', 'like', '%dkm%')
                  ->orWhere('name', 'like', '%DKM%')
                  ->orWhere('name', 'like', '%Operator%');
            })
            ->where('email', 'not like', '%bendahara%')
            ->where('email', 'not like', '%admin%')
            ->update(['role_id' => $petugasId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};