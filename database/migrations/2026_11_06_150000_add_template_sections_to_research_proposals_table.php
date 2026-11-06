<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->text('credentials')->nullable()->after('research_field');
            $table->string('college_affiliation')->nullable()->after('credentials');
            $table->string('sdgs_addressed')->nullable()->after('college_affiliation');
            $table->longText('background_of_study')->nullable()->after('sdgs_addressed');
            $table->longText('significance_of_study')->nullable()->after('background_of_study');
            $table->longText('definition_of_terms')->nullable()->after('significance_of_study');
            $table->longText('literature_review')->nullable()->after('definition_of_terms');
            $table->longText('methodology')->nullable()->after('literature_review');
            $table->longText('references')->nullable()->after('methodology');
            $table->json('work_plan_matrix')->nullable()->after('references');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research_proposals', function (Blueprint $table) {
            $table->dropColumn([
                'credentials',
                'college_affiliation',
                'sdgs_addressed',
                'background_of_study',
                'significance_of_study',
                'definition_of_terms',
                'literature_review',
                'methodology',
                'references',
                'work_plan_matrix'
            ]);
        });
    }
};
