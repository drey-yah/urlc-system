<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResearchProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('research_proposals', function (Blueprint $table) {
    $table->id();

    // Researcher who submitted the proposal
    $table->foreignId('user_id')->constrained()->onDelete('cascade');

    // Proposal details
    $table->string('title');
    $table->text('abstract');
    $table->string('research_field')->nullable();

    // Budget
    $table->decimal('budget_requested', 12, 2)->default(0);
    $table->decimal('budget_spent', 12, 2)->default(0);

    // Workflow status
    $table->string('status')->default('pending');

    // Reviewer/admin feedback
    $table->text('review_comments')->nullable();

    // Uploaded document path
    $table->string('document_path')->nullable();

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
        Schema::dropIfExists('research_proposals');
    }
}
