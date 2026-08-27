<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminalReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminal_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('executive_summary')->nullable();
            $table->string('terminal_report_file')->nullable();
            $table->string('full_paper_file')->nullable();
            $table->string('supporting_docs_file')->nullable();
            $table->decimal('evaluator_score', 5, 2)->nullable();
            $table->text('evaluator_comments')->nullable();
            $table->string('evaluation_form_file')->nullable();
            $table->string('final_report_file')->nullable();
            $table->string('certificate_completion_file')->nullable();
            $table->string('status')->default('submitted_to_unit');
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
        Schema::dropIfExists('terminal_reports');
    }
}
