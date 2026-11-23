<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaUmrahBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('visa_umrah_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_type'); // visa | umrah
            $table->string('booking_no')->unique();
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('nationality')->nullable();

            // Visa specific
            $table->string('visa_country')->nullable();
            $table->string('visa_type')->nullable();
            $table->integer('num_travelers')->nullable();
            $table->date('travel_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('visa_status')
                ->nullable()
                ->default('pending'); // pending, documents_required, processing, approved, rejected, completed, cancelled

            // Umrah specific
            $table->integer('num_adults')->nullable();
            $table->integer('num_children')->nullable();
            $table->string('umrah_package')->nullable();
            $table->string('booking_status')->nullable(); // new, confirmed, active, completed, cancelled

            // Main service (visa / umrah itself) agency + financials
            $table->unsignedBigInteger('main_agency_id')->nullable();
            $table->string('main_agency_reference')->nullable();
            $table->decimal('main_agency_cost', 12, 2)->default(0);    // payable to agency for main service
            $table->decimal('main_client_total', 12, 2)->default(0);    // charged to client for main service

            // Financial summary (auto-maintained)
            $table->decimal('client_total', 12, 2)->default(0);   // main + services
            $table->decimal('client_received', 12, 2)->default(0);
            $table->decimal('agency_total', 12, 2)->default(0);   // main + services payable
            $table->decimal('agency_paid', 12, 2)->default(0);
            $table->decimal('additional_expenses', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visa_umrah_bookings');
    }
}