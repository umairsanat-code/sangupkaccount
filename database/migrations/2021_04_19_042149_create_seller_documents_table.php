<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->enum('status',['REJECTED','PENDING','APPROVED']);
            $table->text('rejectedReason')->nullable();
            $table->string('latitude',100);
            $table->string('longitude',100);
            $table->string('address',500);
            $table->unsignedInteger('createdAt')->nullable();
            $table->unsignedInteger('updatedAt')->nullable();
            $table->unsignedInteger('deletedAt')->nullable();
            $table->foreign('userId')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_documents');
    }
}
