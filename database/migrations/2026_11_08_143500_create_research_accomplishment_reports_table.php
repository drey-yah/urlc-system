<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResearchAccomplishmentReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('research_accomplishment_reports', function (Blueprint $table) {
            $table->id();
            $table->string('form_code')->default('RESU - FM-004');
            $table->string('revision_no')->default('Rev. 3/08-11-25');
            $table->string('college');
            $table->string('quarter')->default('1st Quarter');
            $table->string('school_year')->default('SY 2026-2027');
            $table->integer('fiscal_year')->nullable();
            $table->date('report_date');

            $table->foreignId('prepared_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('noted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('noted_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('received_at')->nullable();

            $table->string('status')->default('pending_dean_noting'); // pending_dean_noting, submitted_to_director, acknowledged

            // Sections A through H as JSON structures
            $table->json('section_a')->nullable(); // Targets vs Accomplishment
            $table->json('section_b')->nullable(); // Activities Conducted & Attended
            $table->json('section_c')->nullable(); // On-Going Researches
            $table->json('section_d')->nullable(); // Completed Researches
            $table->json('section_e')->nullable(); // Research Presentation
            $table->json('section_f')->nullable(); // Publication
            $table->json('section_g')->nullable(); // Utilization
            $table->json('section_h')->nullable(); // Evidences & Pictures

            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('research_accomplishment_reports');
    }
}
