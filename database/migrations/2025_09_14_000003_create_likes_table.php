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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('likeable');
            $table->timestamps();
            
            // Уникальный индекс, чтобы пользователь мог поставить лайк только один раз
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
            
            // Индексы для оптимизации запросов
            $table->index(['likeable_id', 'likeable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
