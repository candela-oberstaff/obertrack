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
        Schema::create('mass_email_logs', function (Blueprint $column) {
            $column->id();
            $column->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $column->string('segment');
            $column->string('subject');
            $column->integer('recipient_count');
            $column->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('set null');
            $column->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mass_email_logs');
    }
};
