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
        Schema::table('expense_tag', function (Blueprint $table) {
            // Add foreign keys only if they don't already exist
            if (!Schema::hasColumn('expense_tag', 'expense_id')) {
                $table->foreignId('expense_id')
                      ->constrained('expenses')
                      ->onDelete('cascade');
            }

            if (!Schema::hasColumn('expense_tag', 'tag_id')) {
                $table->foreignId('tag_id')
                      ->constrained('tags')
                      ->onDelete('cascade');
            }

            // Prevent duplicates
            $table->unique(['expense_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_tag', function (Blueprint $table) {
            $table->dropUnique(['expense_id', 'tag_id']);
            $table->dropForeign(['expense_id']);
            $table->dropForeign(['tag_id']);
            $table->dropColumn(['expense_id', 'tag_id']);
        });
    }
};
