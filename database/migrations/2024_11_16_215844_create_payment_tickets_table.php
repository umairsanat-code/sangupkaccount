<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade'); // Links payment to a sale
            $table->decimal('amount_paid', 10, 2); // Amount paid
            $table->string('payment_method'); // e.g., Bank Transfer, Card, Cash
            $table->string('bank_name')->nullable(); // Bank name, if applicable
            $table->string('card_number')->nullable(); // Card number, if applicable
            $table->date('payment_date'); // Payment date
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
        Schema::dropIfExists('payment_tickets');
    }
}
