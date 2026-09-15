<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFundingAgencyIdentityFields extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('organization')->nullable()->after('department');
        });

        Schema::table('external_funding_projects', function (Blueprint $table) {
            $table->foreignId('funding_agency_user_id')->nullable()->after('research_proposal_id')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('external_funding_projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('funding_agency_user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('organization');
        });
    }
}
