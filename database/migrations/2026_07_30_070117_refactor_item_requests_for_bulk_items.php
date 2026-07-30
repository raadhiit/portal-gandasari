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
        });

        if (Schema::hasColumn('item_requests', 'created_item_id')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->dropForeign(['created_item_id']);
            });
        }

        if (Schema::hasColumn('item_requests', 'category_id')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
            });
        }

        $columns = array_values(array_filter([
            Schema::hasColumn('item_requests', 'unit_id') ? 'unit_id' : null,
            Schema::hasColumn('item_requests', 'warehouse_id') ? 'warehouse_id' : null,
            Schema::hasColumn('item_requests', 'category_id') ? 'category_id' : null,
            Schema::hasColumn('item_requests', 'coa_id') ? 'coa_id' : null,
            Schema::hasColumn('item_requests', 'created_item_id') ? 'created_item_id' : null,
            Schema::hasColumn('item_requests', 'item_name') ? 'item_name' : null,
            Schema::hasColumn('item_requests', 'item_type') ? 'item_type' : null,
            Schema::hasColumn('item_requests', 'item_code') ? 'item_code' : null,
        ]));

        if ($columns !== []) {
            Schema::table('item_requests', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->foreignId('unit_id')->after('department_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('warehouse_id')->after('unit_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->after('warehouse_id')->constrained('item_categories')->nullOnDelete();
            $table->foreignId('coa_id')->nullable()->after('category_id')->constrained('coas')->nullOnDelete();
            $table->foreignId('created_item_id')->nullable()->after('coa_id')->constrained('items')->nullOnDelete();
            $table->string('item_name', 150)->after('assigned_accounting_id');
            $table->string('item_type')->after('item_name');
            $table->string('item_code')->nullable()->unique()->after('item_type');
            $table->dropForeign(['business_unit_id']);
            $table->dropColumn('business_unit_id');
        });
    }
};
