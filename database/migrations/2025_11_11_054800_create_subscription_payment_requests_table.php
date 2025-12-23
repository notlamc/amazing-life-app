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
        Schema::create('subscription_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subscription_id');
            $table->decimal('amount', 10, 2);
            $table->string('paypal_order_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('request_payload')->nullable();
            $table->timestamps();
            $table->timestamp('payment_time')->nullable(); // Added timestamp for PayPal capture time
            $table->foreign('user_id', 'sub_pay_req_user_fk')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscription_id', 'sub_pay_req_sub_fk')->references('id')->on('subscriptions')->onDelete('cascade');

        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_payment_requests');
    }
};
