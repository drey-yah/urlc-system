<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesToResearchMilestonesTable extends Migration
{
    public function up()
    {
        Schema::table('research_milestones', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('target_date')->nullable()->after('start_date');
        });
    }

    public function down()
    {
        Schema::table('research_milestones', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'target_date']);
        });
    }
}
