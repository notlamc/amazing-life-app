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
        Schema::create('offline_downloads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_id');

            // Local file path or encrypted identifier from device
            $table->string('device_file_id')->nullable();

            // Download valid until (like YouTube)
            $table->timestamp('expires_at')->nullable();

            // Last synced watch progress
            $table->integer('last_watched_second')->default(0);
            $table->integer('duration')->default(0);
            $table->float('progress_percent')->default(0);

            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offline_downloads');
    }
};
