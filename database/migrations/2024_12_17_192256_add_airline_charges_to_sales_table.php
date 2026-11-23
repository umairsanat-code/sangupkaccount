<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAirlineChargesToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('refund_amount', 10, 2)->default(0)->after('sale_price'); // Refund amount
            $table->text('refund_reason')->nullable()->after('refund_amount'); // Refund reason
            $table->decimal('service_charges', 10, 2)->default(0)->after('refund_reason'); // Service charges
            $table->decimal('airline_charges', 10, 2)->default(0)->after('service_charges'); // Airline charges

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_reason', 'service_charges', 'airline_charges']);

        });
    }
}
