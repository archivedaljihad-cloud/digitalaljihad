<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to enable Row Level Security on all public tables in PostgreSQL / Supabase.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            foreach ($tables as $t) {
                $tableName = $t->tablename;
                DB::statement("ALTER TABLE public.{$tableName} ENABLE ROW LEVEL SECURITY;");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            foreach ($tables as $t) {
                $tableName = $t->tablename;
                DB::statement("ALTER TABLE public.{$tableName} DISABLE ROW LEVEL SECURITY;");
            }
        }
    }
};
