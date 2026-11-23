<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaUmrahExpensesTable extends Migration
{
    public function up()
    {
        Schema::create('visa_umrah_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visa_umrah_expenses');
    }
}