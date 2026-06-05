<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'product_categories',
            'sales',
            'sale_items',
            'stock_transfers',
            'transfer_items',
            'returns',
            'bank_deposits',
            'stock_movements',
            'financial_years',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'product_categories',
            'sales',
            'sale_items',
            'stock_transfers',
            'transfer_items',
            'returns',
            'bank_deposits',
            'stock_movements',
            'financial_years',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
