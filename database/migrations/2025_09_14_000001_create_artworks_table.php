<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('year')->nullable();
            $table->string('size')->nullable();
            $table->string('materials')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image_path');
            $table->string('image_alt')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            
            $table->index('slug');
            $table->index('is_available');
            $table->index('is_featured');
            $table->index('category_id');
            $table->index('user_id');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
