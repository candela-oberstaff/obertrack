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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cannot easily revert to NOT NULL without knowing if data contains nulls,
            // but for revert sake we assume we can.
            // However, revert usually isn't critical here.
            // We'll leave it nullable or try to strict it back (risky).
            // Better to just not enforce strictly in down or set default.
        });
    }
};
