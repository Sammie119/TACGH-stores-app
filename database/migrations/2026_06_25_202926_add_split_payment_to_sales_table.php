<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('split_method_1')->nullable()->after('payment_method');
            $table->decimal('split_amount_1', 10, 2)->nullable()->after('split_method_1');
            $table->string('split_method_2')->nullable()->after('split_amount_1');
            $table->decimal('split_amount_2', 10, 2)->nullable()->after('split_method_2');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['split_method_1', 'split_amount_1', 'split_method_2', 'split_amount_2']);
        });
    }
};
