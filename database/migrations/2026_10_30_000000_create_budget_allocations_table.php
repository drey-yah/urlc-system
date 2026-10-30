<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->integer('fiscal_year')->default(2026);
            $table->string('campus')->default('Main Campus');
            $table->string('college')->nullable(); // e.g. College of Computing and Information Sciences
            $table->string('program_name'); // e.g. Bachelor of Science in Information Technology
            $table->integer('sequence_no')->nullable(); // order/index within college
            $table->decimal('implementation_pct', 8, 5)->default(0);
            $table->decimal('traveling_pct', 8, 5)->default(0);
            $table->decimal('traveling_local_gaa', 12, 2)->default(0);
            $table->decimal('traveling_local_tf', 12, 2)->default(0);
            $table->decimal('traveling_foreign_tf', 12, 2)->default(0);
            $table->decimal('implementation_tf', 12, 2)->default(0);
            $table->decimal('total_allocation', 12, 2)->default(0);
            $table->string('source_of_fund')->nullable(); // e.g. 'Source of Fund: GAA'
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fiscal_year', 'campus']);
            $table->index('college');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE public.budget_allocations ENABLE ROW LEVEL SECURITY;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_allocations');
    }
};
