<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // who purchased for
            $table->unsignedBigInteger('purchased_id')->nullable(); // payment Id purchased 
            $table->unsignedBigInteger('purchased_by')->nullable(); // who purchased 
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('subscription_price', 10, 2);
            $table->decimal('commission_percentage',5,2);
            $table->decimal('balance_amount',5,2);
            $table->decimal('commission_amount',10,2);
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
