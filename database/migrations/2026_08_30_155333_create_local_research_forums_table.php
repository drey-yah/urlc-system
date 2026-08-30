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
        Schema::create('local_research_forums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('theme')->nullable();
            $table->date('event_date')->nullable();
            $table->string('venue')->nullable();
            $table->date('submission_deadline')->nullable();
            $table->text('guidelines')->nullable();
            $table->string('status')->default('open'); // open, ongoing, completed, archived
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
        Schema::dropIfExists('local_research_forums');
    }
};
