<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internally_funded_endorsements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('fiscal_year')->nullable();
            $table->date('report_date');
            $table->string('status')->default('pending_recommending'); // 'draft', 'pending_recommending', 'pending_president_approval', 'approved', 'returned'
            $table->json('proposal_ids')->nullable();
            $table->json('items'); // array of [{project_title, proponents, college, budget, proposal_id}]
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->text('remarks')->nullable();

            // Signatories
            $table->foreignId('prepared_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('prepared_at')->nullable();

            // Recommending Approval: VPREI
            $table->foreignId('vprei_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('vprei_status')->default('pending'); // 'pending', 'recommended', 'returned'
            $table->dateTime('vprei_signed_at')->nullable();
            $table->text('vprei_remarks')->nullable();

            // Recommending Approval: VPAF
            $table->foreignId('vpaf_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('vpaf_status')->default('pending'); // 'pending', 'recommended', 'returned'
            $table->dateTime('vpaf_signed_at')->nullable();
            $table->text('vpaf_remarks')->nullable();

            // Final Approval: President
            $table->foreignId('president_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('president_status')->default('pending'); // 'pending', 'approved', 'returned'
            $table->dateTime('president_signed_at')->nullable();
            $table->text('president_remarks')->nullable();

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
        Schema::dropIfExists('internally_funded_endorsements');
    }
};
