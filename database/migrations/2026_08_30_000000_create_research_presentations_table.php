<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('research_presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('sponsoring_agency');
            $table->string('conference_name');
            $table->enum('presentation_type', ['oral', 'poster'])->default('oral');
            $table->string('presentation_title');
            $table->date('event_date')->nullable();
            $table->string('venue')->nullable();
            $table->string('acceptance_letter_path')->nullable();
            $table->string('presentation_file_path')->nullable();
            $table->string('certificate_path')->nullable();
            $table->boolean('director_recommended')->default(false);
            $table->timestamp('director_recommended_at')->nullable();
            $table->boolean('president_approved')->default(false);
            $table->timestamp('president_approved_at')->nullable();
            $table->string('status')->default('abstract_submitted'); // abstract_submitted, agency_rejected, acceptance_uploaded, paper_uploaded, recommended_to_president, approved_by_president, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_presentations');
    }
};
