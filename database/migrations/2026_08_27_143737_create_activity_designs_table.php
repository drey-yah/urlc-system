<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityDesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('activity_title');
            $table->string('venue')->nullable();
            $table->date('target_date')->nullable();
            $table->text('objectives')->nullable();
            $table->text('target_participants')->nullable();
            $table->decimal('proposed_budget', 12, 2)->default(0);
            $table->string('activity_design_file')->nullable(); // HRU-FM-021
            $table->string('budget_requirement_file')->nullable(); // BU-FM-006
            $table->boolean('director_noted')->default(false);
            $table->timestamp('director_noted_at')->nullable();
            $table->boolean('budget_officer_noted')->default(false);
            $table->timestamp('budget_officer_noted_at')->nullable();
            $table->boolean('vprei_approved')->default(false);
            $table->timestamp('vprei_approved_at')->nullable();
            $table->string('status')->default('pending_director_noting');
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
        Schema::dropIfExists('activity_designs');
    }
}
