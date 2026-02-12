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
        Schema::create('asset_procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
            $table->string('item_name');
            $table->foreignId('category_id')->nullable()->constrained('asset_categories');
            $table->integer('quantity')->default(1);
            $table->decimal('estimated_price', 15, 2);
            $table->text('justification')->comment('Lý do mua');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Purchased'])->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('approved_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('finance_expense_id')->nullable()->comment('Link to Finance module (future)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_procurement_requests');
    }
};
