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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status')->default('por_hacer')->after('priority');
        });

        // Migrate existing data based on 'completed' boolean
        DB::table('tasks')->where('completed', true)->update(['status' => 'finalizado']);
        DB::table('tasks')->where('completed', false)->update(['status' => 'por_hacer']); 
        // Note: We default 'completed=false' to 'por_hacer'. 'en_proceso' is a new state.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
