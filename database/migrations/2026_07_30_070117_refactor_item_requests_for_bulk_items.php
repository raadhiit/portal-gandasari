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
        Schema::table('item_requests', function (Blueprint $table) {
            $table->foreignId('business_unit_id')->nullable()->after('department_id')->constrained('business_units')->nullOnDelete();
            $table->dropForeign(['created_item_id']);
            $table->dropForeign(['category_id']);
            $table->dropUnique(['item_code']);
            $table->dropColumn(['category_id', 'created_item_id', 'item_name', 'item_type', 'item_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('warehouse_id')->constrained('item_categories')->nullOnDelete();
            $table->foreignId('created_item_id')->nullable()->after('coa_id')->constrained('items')->nullOnDelete();
            $table->string('item_name', 150)->after('assigned_accounting_id');
            $table->string('item_type')->after('item_name');
            $table->string('item_code')->nullable()->unique()->after('item_type');
            $table->dropForeign(['business_unit_id']);
            $table->dropColumn('business_unit_id');
        });
    }
};
