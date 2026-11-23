<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailySaleTicketPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_sale_ticket_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_sale_ticket_id');
            $table->double('amount');
            $table->date('payment_date');
            $table->string('payment_method')->nullable(); // Cash / Bank / Card
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_sale_ticket_payments');
    }
}
