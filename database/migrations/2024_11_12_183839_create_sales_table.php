<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->decimal('sale_price', 10, 2);
            $table->date('sale_date');
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->enum('payment_status', ['Paid', 'Pending'])->default('Pending');
            $table->decimal('total_amount_paid', 10, 2)->default(0); // Total amount received
            $table->decimal('pending_amount', 10, 2)->nullable();
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
        Schema::dropIfExists('sales');
    }
}
