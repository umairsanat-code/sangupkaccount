<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->bigIncrements('id')->autoIncrement();
            $table->string('email_phoneNumber')->unique();
            $table->string('otp',10);
            $table->string('otpType');
            $table->boolean('isVerified')->default('0');
            $table->unsignedInteger('createdAt')->nullable();
            $table->unsignedInteger('updatedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
}
