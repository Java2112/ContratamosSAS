<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contracting_process_id')->constrained()->cascadeOnDelete();
            $table->string('provider_name')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->string('result')->nullable(); // Uses MedicalResult enum
            $table->text('observations')->nullable();
            $table->string('file_path')->nullable(); // Upload result PDF
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_exams');
    }
};
