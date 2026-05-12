<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRationaleToResearchProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->text('rationale')->nullable()->after('abstract');
        });
    }

    public function down()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->dropColumn('rationale');
        });
    }
}
