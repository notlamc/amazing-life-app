<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');                            // Template name
            $table->enum('type', ['email', 'push']);           // Email or Push
            $table->string('subject')->nullable();             // Email only
            $table->longText('body');                          // Template content (HTML/Text)
            $table->json('variables')->nullable();             // Store dynamic variables like {name}, {otp}, etc.
            $table->boolean('status')->default(true);          // Active/Inactive
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_templates');
    }
};
