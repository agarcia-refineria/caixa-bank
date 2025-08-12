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
        Schema::table('transactions', function (Blueprint $table) {
            $table->json('data')->nullable();

            $table->dropColumn('entryReference');
            $table->dropColumn('checkId');
            $table->dropColumn('bankTransactionCode');
            $table->dropColumn('proprietaryBankTransactionCode');
            $table->dropColumn('internalTransactionId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('data');

            $table->string('entryReference')->nullable();
            $table->string('checkId')->nullable();
            $table->string('bankTransactionCode')->nullable();
            $table->string('proprietaryBankTransactionCode')->nullable();
            $table->string('internalTransactionId')->nullable();
        });
    }
};
