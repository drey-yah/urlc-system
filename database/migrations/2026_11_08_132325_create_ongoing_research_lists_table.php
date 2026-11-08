<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOngoingResearchListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ongoing_research_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('List of On-going Researches');
            $table->integer('fiscal_year')->default(date('Y'));
            $table->date('report_date')->nullable();
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
        Schema::dropIfExists('ongoing_research_lists');
    }
}
