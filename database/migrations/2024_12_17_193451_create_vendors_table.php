<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');  // Reference to Agent
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');  // Reference to Ticket
            $table->decimal('payment_amount', 10, 2);  // Payment amount
            $table->string('payment_method')->nullable();  // Payment method (optional)
            $table->date('start_date');  // Start date of the ticket
            $table->date('end_date');  // End date of the ticket
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
        Schema::dropIfExists('vendors');
    }
}
