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
        Schema::create('proposal_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Reviewer
            $table->decimal('score_a', 5, 2)->default(0); // Innovativeness/Novelty/Creativity (15%)
            $table->decimal('score_b', 5, 2)->default(0); // Time Schedule and Cost (5%)
            $table->decimal('score_c', 5, 2)->default(0); // Applicability & Potential IPSs (25%)
            $table->decimal('score_d', 5, 2)->default(0); // Relevance (25%)
            $table->decimal('score_e', 5, 2)->default(0); // Effectiveness (30%)
            $table->decimal('total_score', 5, 2)->default(0); // Total Rating (%)
            $table->boolean('meets_threshold')->default(false); // >= 80%
            $table->string('decision')->default('pending'); // approved, approved_with_revisions, revision_required, rejected
            $table->text('comments')->nullable();
            $table->text('suggestions')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            $table->unique(['research_proposal_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_evaluations');
    }
};
