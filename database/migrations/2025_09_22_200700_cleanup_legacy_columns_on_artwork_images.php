<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artwork_images', function (Blueprint $table) {
            
            if (Schema::hasColumn('artwork_images', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('artwork_images', 'file_size')) {
                $table->dropColumn('file_size');
            }
            if (Schema::hasColumn('artwork_images', 'order_column')) {
                $table->dropColumn('order_column');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artwork_images', function (Blueprint $table) {
            
        });
    }
};
