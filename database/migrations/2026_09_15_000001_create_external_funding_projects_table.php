<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalFundingProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('external_funding_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->unique()->constrained()->onDelete('cascade');
            $table->string('funding_agency');
            $table->string('grant_program')->nullable();
            $table->string('agency_reference_number')->nullable();
            $table->decimal('requested_amount', 14, 2)->nullable();
            $table->decimal('awarded_amount', 14, 2)->nullable();
            $table->string('status')->default('pending_university_endorsement');
            $table->text('agency_feedback')->nullable();
            $table->string('moa_mou_path')->nullable();
            $table->date('grant_start_date')->nullable();
            $table->date('grant_end_date')->nullable();
            $table->string('terminal_report_path')->nullable();
            $table->timestamp('university_endorsed_at')->nullable();
            $table->timestamp('president_endorsed_at')->nullable();
            $table->timestamp('agency_decided_at')->nullable();
            $table->timestamp('agreement_executed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_funding_projects');
    }
}
