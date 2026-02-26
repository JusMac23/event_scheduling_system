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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // Links the schedule to the user who created it. 
            // cascadeOnDelete() means if the user is deleted, their schedules are deleted too.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Event details
            $table->string('title');
            $table->text('description')->nullable();
            
            // Timeframes
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            
            // Status of the schedule (e.g., pending, confirmed, cancelled)
            $table->string('status')->default('pending');
            
            // Created at & Updated at timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};