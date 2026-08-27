<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('period_covered');
            $table->text('progress_summary')->nullable();
            $table->string('monitoring_form_path')->nullable();
            $table->boolean('coordinator_verified')->default(false);
            $table->timestamp('coordinator_verified_at')->nullable();
            $table->string('status')->default('submitted');
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
        Schema::dropIfExists('project_monitorings');
    }
}
