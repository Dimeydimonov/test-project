<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('artwork_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained('artworks')->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name')->nullable();
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->index(['artwork_id','order']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('artwork_images');
    }
};
