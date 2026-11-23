<?php

use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blockedBy')->comment('Id of user who added the User Blocked');
            $table->foreign('blockedBy')->references('id')->on('users');
            $table->unsignedBigInteger('blockedUserId')->comment('Blocked this id');
            $table->foreign('blockedUserId')->references('id')->on('users');
            $table->enum('status',[UserStatus::BLOCK,UserStatus::UNBLOCK]);
            $table->unsignedInteger('createdAt');
            $table->unsignedInteger('updatedAt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_blocks');
    }
}
