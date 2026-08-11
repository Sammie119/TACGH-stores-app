<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('transaction_reference', 100)->nullable()->after('payment_method');
            $table->string('split_reference_1', 100)->nullable()->after('split_amount_1');
            $table->string('split_reference_2', 100)->nullable()->after('split_amount_2');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['transaction_reference', 'split_reference_1', 'split_reference_2']);
        });
    }
};
