<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaUmrahServicesTable extends Migration
{
    public function up()
    {
        Schema::create('visa_umrah_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('service_type'); // hotel | transport
            $table->string('name')->nullable();          // hotel name / vehicle service name
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->integer('num_nights')->nullable();
            $table->string('room_type')->nullable();
            $table->integer('num_rooms')->nullable();
            $table->integer('num_guests')->nullable();
            $table->string('meal_plan')->nullable();      // room_only, breakfast, half_board, full_board
            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->integer('num_passengers')->nullable();

            $table->unsignedBigInteger('agency_id')->nullable();
            $table->string('agency_reference')->nullable();
            $table->decimal('agency_cost', 12, 2)->default(0);
            $table->decimal('client_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visa_umrah_services');
    }
}