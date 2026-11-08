<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCreditListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_credit_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('List of Researchers Qualified to Avail Service Credits');
            $table->integer('fiscal_year')->nullable();
            $table->date('report_date');
            $table->foreignId('prepared_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->string('status')->default('pending_approval'); // pending_approval, approved, rejected
            $table->json('proposal_ids');
            $table->json('items')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_credit_lists');
    }
}
