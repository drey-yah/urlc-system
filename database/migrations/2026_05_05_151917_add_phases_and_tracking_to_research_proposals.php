<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhasesAndTrackingToResearchProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->integer('current_phase')->default(1)->after('status');
            $table->decimal('budget_approved', 15, 2)->nullable()->after('budget_requested');
            $table->string('terminal_report_path')->nullable()->after('document_path');
            $table->timestamp('phase_updated_at')->nullable()->after('current_phase');
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
            $table->dropColumn(['current_phase', 'budget_approved', 'terminal_report_path', 'phase_updated_at']);
        });
    }
}
