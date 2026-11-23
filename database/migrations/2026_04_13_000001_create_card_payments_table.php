<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('card_payments')) {
            return;
        }

        Schema::create('card_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('bank_name', [
                'SANTANDER SANGU',
                'BBVA SANGU',
                'LACIAXA SANGU',
                'BBVA IMRO',
                'LACIAXA IMRO',
            ])->nullable();
            $table->string('card_payment', 50)->nullable();
            $table->decimal('received_amount', 15, 2)->default(0);
            $table->date('received_date')->nullable();
            $table->decimal('sent_amount', 15, 2)->default(0);
            $table->date('sent_date')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('bank_name');
            $table->index('received_date');
            $table->index('sent_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_payments');
    }
};
