<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Migrate existing data
        $tasks = DB::table('tasks')->whereNotNull('visible_para')->get();
        foreach ($tasks as $task) {
            DB::table('task_user')->insert([
                'task_id' => $task->id,
                'user_id' => $task->visible_para,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop the old column
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('visible_para');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('visible_para')->nullable();
        });

        // Restore data (approximate, since we lose multiple assignees if reverting)
        $assignments = DB::table('task_user')->get();
        foreach ($assignments as $assignment) {
            // This will overwrite if multiple users assigned, but it's best effort for down()
            DB::table('tasks')->where('id', $assignment->task_id)->update([
                'visible_para' => $assignment->user_id
            ]);
        }

        Schema::dropIfExists('task_user');
    }
};
