<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalBudgetItemsTable extends Migration
{
    public function up()
    {
        Schema::create('proposal_budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->onDelete('cascade');
            $table->string('category_type')->default('mooe'); // 'mooe' or 'co'
            $table->string('category_group')->default('supplies'); // 'supplies', 'semi_expandable', 'travel', 'transportation', 'professional_services', 'other_mooe', 'capital_outlay'
            $table->text('item_name');
            $table->string('funding_agency')->nullable();
            $table->string('equivalent_teaching_unit')->nullable();
            $table->string('existing_resources')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('research_proposals', function (Blueprint $table) {
            $table->decimal('total_budget', 15, 2)->nullable()->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposal_budget_items');

        Schema::table('research_proposals', function (Blueprint $table) {
            $table->dropColumn('total_budget');
        });
    }
}
