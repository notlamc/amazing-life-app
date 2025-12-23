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
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('gender')->nullable();
            $table->timestamp('email_verified_at')->nullable();   // ➜ Added here    
            $table->string('phone_number')->nullable();     
            $table->string('age')->nullable();     
            $table->string('password')->nullable();
            $table->string('pass')->nullable();        
            $table->string('otp')->nullable();            
            $table->string('profile')->nullable();            
            $table->string('authenticationSocialId')->nullable()->unique();
            $table->string('authenticationProvider')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->string('facebook_id')->nullable()->unique();
            $table->string('apple_id')->nullable()->unique();
            $table->string('avatar')->nullable(); 
            $table->string('status')->default('active'); 
            $table->string('referral_code')->unique();
            $table->unsignedBigInteger('referred_by')->nullable()->comment('user_id of referrer');   
            $table->timestamps();
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');

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
};
