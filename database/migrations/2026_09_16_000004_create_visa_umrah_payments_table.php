<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaUmrahPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('visa_umrah_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_id')->nullable(); // null = main booking service
            $table->string('party'); // client | agency
            $table->string('payment_method'); // cash | bank | card | online
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->string('reference')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visa_umrah_payments');
    }
}