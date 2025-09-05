<?php
// database/migrations/2025_09_05_000002_add_meta_to_payment_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Datos del formulario
            $table->string('ruex')->nullable()->after('reference');
            $table->string('email')->nullable()->after('ruex');
            $table->string('account_number')->nullable()->after('email'); // W-...

            // Campos de respuesta del gateway
            $table->timestamp('request_date')->nullable()->after('account_number');
            $table->timestamp('response_date')->nullable()->after('request_date');

            $table->string('response_code', 16)->nullable()->after('response_date');   // Code
            $table->string('authorization_number', 64)->nullable()->after('response_code');
            $table->string('bin_id', 64)->nullable()->after('authorization_number');
            $table->string('processor_id', 64)->nullable()->after('bin_id');
            $table->string('result', 64)->nullable()->after('processor_id');
            $table->string('tracking', 128)->nullable()->after('result');
            $table->string('system_tracking', 128)->nullable()->after('tracking');

            // índices útiles
            $table->index(['response_code', 'status']);
            $table->index('authorization_number');
            $table->index('gateway_transaction_id');
        });
    }

    public function down(): void {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'ruex','email','account_number',
                'request_date','response_date',
                'response_code','authorization_number','bin_id','processor_id',
                'result','tracking','system_tracking'
            ]);
        });
    }
};
