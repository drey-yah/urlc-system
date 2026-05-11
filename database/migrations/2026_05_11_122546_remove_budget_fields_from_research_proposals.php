<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveBudgetFieldsFromResearchProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->dropColumn(['budget_requested', 'budget_spent', 'budget_approved']);
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
            $table->decimal('budget_requested', 12, 2)->default(0);
            $table->decimal('budget_spent', 12, 2)->default(0);
            $table->decimal('budget_approved', 15, 2)->nullable();
        });
    }
}
