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
        Schema::create('asset_procurements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique()->nullable(); // PM-2024-001
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->text('reason')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'ordered', 'completed'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->decimal('total_estimated_cost', 15, 2)->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('asset_procurements')->cascadeOnDelete();
            $table->string('item_name');
            $table->text('specifications')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price_estimate', 15, 2)->default(0);
            $table->string('supplier_url')->nullable();
            $table->timestamps();
        });
        
        // Table for storing quotes comparisons if needed specifically, 
        // but for now maybe items logic is enough or just use attachments on procurement main table?
        // Plan mentioned: "asset_procurement_items".
        // Plan also mentioned: "Compare quotes: Attach 2-3 quotes".
        // We can add a `quotes` json column or a separate table `asset_procurement_quotes`.
        // Let's add a separate table for cleaner structure.
        
        Schema::create('asset_procurement_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('asset_procurements')->cascadeOnDelete();
            $table->string('supplier_name');
            $table->decimal('total_price', 15, 2);
            $table->string('file_url')->nullable()->comment('Path to quote PDF/Image');
            $table->boolean('is_selected')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_procurement_quotes');
        Schema::dropIfExists('asset_procurement_items');
        Schema::dropIfExists('asset_procurements');
    }
};
