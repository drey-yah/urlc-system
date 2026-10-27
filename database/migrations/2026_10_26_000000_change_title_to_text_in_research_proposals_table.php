<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTitleToTextInResearchProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE research_proposals ALTER COLUMN title TYPE TEXT;');
            DB::statement('ALTER TABLE research_proposals ALTER COLUMN research_field TYPE TEXT;');
        } else {
            Schema::table('research_proposals', function (Blueprint $table) {
                $table->text('title')->change();
                $table->text('research_field')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE research_proposals ALTER COLUMN title TYPE VARCHAR(255);');
            DB::statement('ALTER TABLE research_proposals ALTER COLUMN research_field TYPE VARCHAR(255);');
        } else {
            Schema::table('research_proposals', function (Blueprint $table) {
                $table->string('title', 255)->change();
                $table->string('research_field', 255)->nullable()->change();
            });
        }
    }
}
