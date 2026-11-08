<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCollaborationPreferencesAndStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add allow_collaboration privacy flag to users table
        if (!Schema::hasColumn('users', 'allow_collaboration')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('allow_collaboration')->default(true)->after('department');
            });
        }

        // 2. Add status and timestamp columns to proposal_collaborators table
        if (!Schema::hasColumn('proposal_collaborators', 'status')) {
            Schema::table('proposal_collaborators', function (Blueprint $table) {
                $table->string('status')->default('pending')->after('user_id');
                $table->timestamp('invited_at')->nullable()->after('status');
                $table->timestamp('responded_at')->nullable()->after('invited_at');
            });

            // Mark any pre-existing rows as accepted
            DB::table('proposal_collaborators')->whereNull('status')->orWhere('status', 'pending')->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('proposal_collaborators', 'status')) {
            Schema::table('proposal_collaborators', function (Blueprint $table) {
                $table->dropColumn(['status', 'invited_at', 'responded_at']);
            });
        }

        if (Schema::hasColumn('users', 'allow_collaboration')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('allow_collaboration');
            });
        }
    }
}
