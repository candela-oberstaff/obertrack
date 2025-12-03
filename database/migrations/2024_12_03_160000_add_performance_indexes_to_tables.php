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
        // Add indexes to work_hours table
        Schema::table('work_hours', function (Blueprint $table) {
            $table->index('user_id', 'work_hours_user_id_index');
            $table->index('work_date', 'work_hours_work_date_index');
            $table->index('approved', 'work_hours_approved_index');
            // Composite indexes for common query patterns
            $table->index(['user_id', 'work_date'], 'work_hours_user_date_index');
            $table->index(['user_id', 'approved'], 'work_hours_user_approved_index');
        });

        // Add indexes to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('created_by', 'tasks_created_by_index');
            $table->index('visible_para', 'tasks_visible_para_index');
            $table->index(['created_by', 'completed'], 'tasks_created_completed_index');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('empleador_id', 'users_empleador_id_index');
            $table->index('tipo_usuario', 'users_tipo_usuario_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_hours', function (Blueprint $table) {
            $table->dropIndex('work_hours_user_id_index');
            $table->dropIndex('work_hours_work_date_index');
            $table->dropIndex('work_hours_approved_index');
            $table->dropIndex('work_hours_user_date_index');
            $table->dropIndex('work_hours_user_approved_index');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_created_by_index');
            $table->dropIndex('tasks_visible_para_index');
            $table->dropIndex('tasks_created_completed_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_empleador_id_index');
            $table->dropIndex('users_tipo_usuario_index');
        });
    }
};
