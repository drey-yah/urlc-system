<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_proposal_id')->constrained('research_proposals')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('pr_number')->nullable();
            $table->text('purpose');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('document_path')->nullable();
            $table->boolean('director_countersigned')->default(false);
            $table->timestamp('director_countersigned_at')->nullable();
            $table->boolean('finance_approved')->default(false);
            $table->timestamp('finance_approved_at')->nullable();
            $table->string('status')->default('pending_director_countersign');
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
        Schema::dropIfExists('purchase_requests');
    }
}
