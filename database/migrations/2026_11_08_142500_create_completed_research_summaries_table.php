<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletedResearchSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('completed_research_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('COMPLETED RESEARCHES SUMMARY FORM');
            $table->string('college');
            $table->integer('fiscal_year')->nullable();
            $table->date('report_date');
            $table->foreignId('prepared_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('noted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('noted_at')->nullable();
            $table->string('status')->default('pending_noting'); // pending_noting, noted
            $table->json('proposal_ids');
            $table->json('items')->nullable();
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
        Schema::dropIfExists('completed_research_summaries');
    }
}
