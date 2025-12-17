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
        if (!Schema::hasColumn('tasks', 'visible_para')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->unsignedBigInteger('visible_para')->nullable()->after('created_by');
                // We add the foreign key if it doesn't exist, but usually it's tied to the column.
                // It's safer to add it in a separate try/catch or just add it here knowing the column is new.
                $table->foreign('visible_para')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tasks', 'visible_para')) {
            Schema::table('tasks', function (Blueprint $table) {
                // Drop foreign key first. Convention: table_column_foreign
                $table->dropForeign(['visible_para']);
                $table->dropColumn('visible_para');
            });
        }
    }
};
