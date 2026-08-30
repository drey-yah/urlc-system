<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('research_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Step 1: Letter of Intent & Target Journal Info
            $table->string('intent_letter_path')->nullable();
            $table->string('journal_title')->nullable();
            $table->string('issn_number')->nullable();
            $table->string('indexing_agency')->nullable(); // e.g. CHED Accredited, Scopus, WoS

            // Step 2: IP Screening & Registration
            $table->boolean('has_ip_potential')->default(false);
            $table->text('ip_notes')->nullable();
            $table->string('ip_registration_file_path')->nullable();
            $table->boolean('ip_cleared')->default(false);
            $table->timestamp('ip_cleared_at')->nullable();

            // Step 3: VPREI Publication Authorization
            $table->boolean('vprei_approved')->default(false);
            $table->timestamp('vprei_approved_at')->nullable();

            // Step 4: Submission Proof to Journal
            $table->string('submission_proof_path')->nullable();

            // Step 5: Final Published Copy & Archival
            $table->string('published_copy_path')->nullable();
            $table->string('doi_link')->nullable();

            // Workflow status:
            // intent_submitted, ip_screening_required, ip_registration_required, ip_cleared, approved_for_publication, submitted_to_journal, published_and_archived
            $table->string('status')->default('intent_submitted');

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
        Schema::dropIfExists('research_publications');
    }
};
