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
        // 1. Records
        Schema::create('disciplinary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('record_number')->unique();
            $table->string('witness_name')->nullable();
            $table->string('representative_name');
            $table->string('representative_role');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->text('reason');
            $table->text('rules_violated')->nullable();
            $table->text('introductory_text')->nullable();
            $table->text('initial_observations')->nullable();
            $table->text('final_observations')->nullable();
            $table->string('status')->default('BORRADOR'); // BORRADOR, EN_PROCESO, FINALIZADO, PDF_GENERADO, CERRADO
            $table->string('employee_signature_path')->nullable();
            $table->timestamp('employee_signed_at')->nullable();
            $table->string('employer_signature_path')->nullable();
            $table->timestamp('employer_signed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'employee_id']);
            $table->index('status');
            $table->index('record_number');
        });

        // 2. Questions
        Schema::create('disciplinary_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplinary_record_id')->constrained('disciplinary_records')->cascadeOnDelete();
            $table->text('question_text');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_from_template')->default(false);
            $table->timestamps();

            $table->index('sort_order');
        });

        // 3. Answers
        Schema::create('disciplinary_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplinary_question_id')->constrained('disciplinary_questions')->cascadeOnDelete();
            $table->text('answer_text');
            $table->timestamps();
        });

        // 4. States Log
        Schema::create('disciplinary_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplinary_record_id')->constrained('disciplinary_records')->cascadeOnDelete();
            $table->string('state');
            $table->foreignId('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 5. Files / Versions
        Schema::create('disciplinary_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplinary_record_id')->constrained('disciplinary_records')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->integer('version')->default(1);
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplinary_files');
        Schema::dropIfExists('disciplinary_states');
        Schema::dropIfExists('disciplinary_answers');
        Schema::dropIfExists('disciplinary_questions');
        Schema::dropIfExists('disciplinary_records');
    }
};
