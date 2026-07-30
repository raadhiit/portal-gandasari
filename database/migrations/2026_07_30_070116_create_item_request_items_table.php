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
        Schema::create('item_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_request_id')->constrained('item_requests')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->nullOnDelete();
            $table->foreignId('inventory_coa_id')->nullable()->constrained('coas')->nullOnDelete();
            $table->foreignId('sales_coa_id')->nullable()->constrained('coas')->nullOnDelete();
            $table->foreignId('created_item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('item_name', 150);
            $table->string('item_type');
            $table->string('item_code')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_request_items');
    }
};
