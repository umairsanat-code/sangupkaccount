<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('flight_date');
            $table->date('booking_date');
            $table->string('departure_city');
            $table->string('destination_city');
            $table->enum('class', ['Economy', 'Business', 'First Class']);
            $table->enum('status', ['Booked', 'Cancelled', 'Checked In']);
            $table->enum('refund_status', ['Not Refunded', 'Refunded'])->default('Not Refunded');
            $table->decimal('purchase_price', 10, 2);
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
        Schema::dropIfExists('tickets');
    }
}
