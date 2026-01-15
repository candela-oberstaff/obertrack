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
        Schema::table('work_hours', function (Blueprint $table) {
            $table->decimal('absence_hours', 5, 2)->default(0)->after('hours_worked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_hours', function (Blueprint $table) {
            $table->dropColumn('absence_hours');
        });
    }
};
