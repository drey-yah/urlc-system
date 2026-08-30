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
        Schema::create('local_forum_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_research_forum_id')->constrained('local_research_forums')->cascadeOnDelete();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('paper_title');
            $table->text('abstract')->nullable();
            $table->string('extended_abstract_path')->nullable();
            $table->string('presentation_file_path')->nullable();

            // Coordinator Endorsement (Step 2)
            $table->boolean('coordinator_endorsed')->default(false);
            $table->timestamp('coordinator_endorsed_at')->nullable();

            // Director Notice of Acceptance (Step 4)
            $table->boolean('is_accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->string('notice_of_acceptance_path')->nullable();

            // Presentation Certificate (Step 5)
            $table->string('certificate_path')->nullable();

            // Workflow status:
            // submitted_to_coordinator, endorsed_by_coordinator, accepted_by_director, rejected, presented_and_completed
            $table->string('status')->default('submitted_to_coordinator');

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
        Schema::dropIfExists('local_forum_submissions');
    }
};
