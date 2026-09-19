<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Enables Row Level Security (RLS) on all public schema base tables.
     * This secures tables against direct unauthorized PostgREST/Supabase Data API access
     * under a default-deny policy, while preserving backend access for the direct database connection.
     *
     * @return void
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Fetch all base tables in the public schema
        $tables = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
              AND table_type = 'BASE TABLE'
        ");

        foreach ($tables as $table) {
            $tableName = $table->table_name;
            DB::statement("ALTER TABLE public.{$tableName} ENABLE ROW LEVEL SECURITY;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $tables = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
              AND table_type = 'BASE TABLE'
        ");

        foreach ($tables as $table) {
            $tableName = $table->table_name;
            DB::statement("ALTER TABLE public.{$tableName} DISABLE ROW LEVEL SECURITY;");
        }
    }
};
