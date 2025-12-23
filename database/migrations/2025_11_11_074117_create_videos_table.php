<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('tags')->nullable();
            $table->text('metatags')->nullable();
            $table->text('description')->nullable();
            $table->string('categories')->nullable();
            $table->string('video_path'); // video file path (local or S3)
            $table->string('thumbnail_path')->nullable(); // optional thumbnail
            $table->integer('duration')->nullable()->comment('Duration in seconds');
            $table->enum('status', ['active', 'deactive'])->default('active');
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->timestamps();

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
    