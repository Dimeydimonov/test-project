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
        // Check if the deleted_at column doesn't exist before adding it
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
            
            // Column added successfully
        } else {
            // Column already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop the column if it exists
        if (Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
            
            // Column removed successfully
        } else {
            // Column does not exist
        }
    }
};
