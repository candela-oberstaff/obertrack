<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('tasks', function (Blueprint $table) {
    //         $table->id();
    //         $table->unsignedBigInteger('created_by');
    //         $table->unsignedBigInteger('visible_para')->nullable(); // Nuevo campo para el ID del empleador
    //         $table->string('title');
    //         $table->text('description')->nullable();
    //         $table->boolean('completed')->default(false);
    //         // $table->integer('duration')->nullable();
    //         $table->decimal('duration', 4, 2)->nullable();

            
    //         $table->timestamps();
        
    //         $table->foreign('created_by')->references('id')->on('users');
    //     });
        
    // }

        public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('visible_para')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('completed')->default(false);
            // $table->decimal('duration', 4, 2)->nullable();
            $table->timestamps();
        
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
