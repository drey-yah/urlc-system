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
        Schema::create('proposal_annotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tagged_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tagged_email')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('proposal_annotations')->onDelete('cascade');
            $table->string('chapter_section')->default('General');
            $table->text('highlighted_text')->nullable();
            $table->string('highlight_anchor')->nullable();
            $table->text('comment');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_annotations');
    }
};
