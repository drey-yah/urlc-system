<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewerIdToResearchProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewer_id');
        });
    }
}
