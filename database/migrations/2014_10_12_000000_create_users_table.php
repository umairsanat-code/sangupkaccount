<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstName',150);
            $table->string('lastName',150);
            $table->string('email',150)->unique();
            $table->string('phoneNumber',100)->unique()->nullable();
            $table->string('password',255);
            $table->string('avatar', 255)->nullable();
            $table->string('uid', 100)->unique()->nullable();
            $table->string('paypalCustomerId',250)->unique()->nullable();
            $table->enum('status', ['Active', 'Banned'])->default('Active');
            $table->string('fbId',250)->unique()->nullable();
            $table->string('googleId',250)->unique()->nullable();
            $table->string('appleId',250)->unique()->nullable();
            $table->decimal('sellerRating',5,1)->default(0);
            $table->decimal('sellerReviews',5,1)->default(0);
            $table->decimal('buyerRating',5,1)->default(0);
            $table->decimal('buyerReviews',5,1)->default(0);
            $table->rememberToken();
            $table->unsignedInteger('createdAt')->nullable();
            $table->unsignedInteger('updatedAt')->nullable();
            $table->unsignedInteger('deletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
