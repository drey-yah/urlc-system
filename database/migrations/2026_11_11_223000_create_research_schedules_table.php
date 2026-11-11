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
        Schema::create('research_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('event_type'); // 'call_for_paper', 'in_house_review', 'inception_meeting', 'other'
            $table->string('academic_year')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->dateTime('submission_deadline')->nullable();
            $table->string('venue')->nullable();
            $table->string('meeting_link')->nullable();
            $table->json('target_audience')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('status')->default('published'); // 'published', 'completed', 'cancelled'
            $table->foreignId('announcement_id')->nullable()->constrained('announcements')->nullOnDelete();
            $table->json('reminders_sent')->nullable();
            $table->timestamps();
        });

        Schema::create('schedule_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_schedule_id')->constrained('research_schedules')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('confirmed'); // 'confirmed', 'declined', 'tentative'
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['research_schedule_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_attendees');
        Schema::dropIfExists('research_schedules');
    }
};
