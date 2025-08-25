<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payment_tokens', function (Blueprint $table) {
            $table->string('account_number')->after('token')->nullable();
            $table->string('cardholder_name')->after('brand')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payment_tokens', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'cardholder_name']);
        });
    }
};