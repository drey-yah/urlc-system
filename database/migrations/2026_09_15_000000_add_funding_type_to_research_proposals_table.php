<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFundingTypeToResearchProposalsTable extends Migration
{
    public function up()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->string('funding_type')->default('internal')->after('status');
        });
    }

    public function down()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->dropColumn('funding_type');
        });
    }
}
