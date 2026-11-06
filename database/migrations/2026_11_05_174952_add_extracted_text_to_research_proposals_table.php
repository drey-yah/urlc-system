<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtractedTextToResearchProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->longText('extracted_text')->nullable()->after('terminal_report_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->dropColumn('extracted_text');
        });
    }
}
