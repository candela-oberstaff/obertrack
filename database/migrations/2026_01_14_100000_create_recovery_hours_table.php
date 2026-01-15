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
        Schema::create('recovery_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('recovery_date'); // When recovery was done (always today)
            $table->decimal('hours_recovered', 5, 2); // Hours being recovered
            $table->text('activities')->nullable(); // What tasks were done
            $table->boolean('approved')->default(false); // Employer approval
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'recovery_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recovery_hours');
    }
};
