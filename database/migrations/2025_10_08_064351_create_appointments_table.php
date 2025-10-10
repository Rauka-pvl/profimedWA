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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('service');
            $table->date('date');
            $table->string('time');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['doctor_id', 'date']);
            $table->index(['patient_id', 'date']);
            $table->unique(['doctor_id', 'patient_id', 'date', 'time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
