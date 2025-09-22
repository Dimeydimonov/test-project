<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artwork_images', function (Blueprint $table) {
            if (!Schema::hasColumn('artwork_images', 'artwork_id')) {
                $table->foreignId('artwork_id')->after('id')->constrained('artworks')->onDelete('cascade');
            }
            if (!Schema::hasColumn('artwork_images', 'filename')) {
                $table->string('filename')->after('artwork_id');
            }
            if (!Schema::hasColumn('artwork_images', 'original_name')) {
                $table->string('original_name')->nullable()->after('filename');
            }
            if (!Schema::hasColumn('artwork_images', 'path')) {
                $table->string('path')->after('original_name');
            }
            if (!Schema::hasColumn('artwork_images', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('path');
            }
            if (!Schema::hasColumn('artwork_images', 'size')) {
                $table->unsignedBigInteger('size')->default(0)->after('mime_type');
            }
            if (!Schema::hasColumn('artwork_images', 'order')) {
                $table->unsignedInteger('order')->default(0)->after('size');
            }
            if (!Schema::hasColumn('artwork_images', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('order');
            }
            
            try {
                $table->index(['artwork_id', 'order']);
            } catch (\Throwable $e) {
                
            }
        });
    }

    public function down(): void
    {
        Schema::table('artwork_images', function (Blueprint $table) {
            if (Schema::hasColumn('artwork_images', 'is_primary')) {
                $table->dropColumn('is_primary');
            }
            if (Schema::hasColumn('artwork_images', 'order')) {
                $table->dropColumn('order');
            }
            if (Schema::hasColumn('artwork_images', 'size')) {
                $table->dropColumn('size');
            }
            if (Schema::hasColumn('artwork_images', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
            if (Schema::hasColumn('artwork_images', 'path')) {
                $table->dropColumn('path');
            }
            if (Schema::hasColumn('artwork_images', 'original_name')) {
                $table->dropColumn('original_name');
            }
            if (Schema::hasColumn('artwork_images', 'filename')) {
                $table->dropColumn('filename');
            }
            
            if (Schema::hasColumn('artwork_images', 'artwork_id')) {
                $table->dropConstrainedForeignId('artwork_id');
            }
        });
    }
};
