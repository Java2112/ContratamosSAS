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
        Schema::table('vacancies', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('employer_type')->nullable(); // directa, contratamos
            $table->string('contract_type')->nullable(); 
            $table->string('payroll_frequency')->nullable();
            $table->string('workday_type')->nullable();
            $table->string('schedule')->nullable();
            
            // Salary
            $table->string('salary_type')->nullable(); // defined, to_agree
            $table->decimal('min_salary', 15, 2)->nullable();
            $table->decimal('max_salary', 15, 2)->nullable();
            $table->boolean('has_bonuses')->default(false);
            $table->decimal('bonus_average', 15, 2)->nullable();

            // Modality & Location
            $table->string('work_modality')->nullable(); // presencial, hibrido, remoto
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('department_name')->nullable();

            // Candidate Requirements
            $table->string('min_education_level')->nullable();
            $table->integer('min_experience_years')->nullable();
            $table->json('languages')->nullable();
            $table->json('soft_skills')->nullable();
            $table->json('hard_skills')->nullable();

            // Description
            $table->text('main_functions')->nullable();
            $table->text('optional_features')->nullable();
            $table->integer('estimated_duration_months')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn([
                'department', 'employer_type', 'contract_type', 'payroll_frequency', 
                'workday_type', 'schedule', 'salary_type', 'min_salary', 'max_salary', 
                'has_bonuses', 'bonus_average', 'work_modality', 'address', 'city', 
                'department_name', 'min_education_level', 'min_experience_years', 
                'languages', 'soft_skills', 'hard_skills', 'main_functions', 
                'optional_features', 'estimated_duration_months'
            ]);
        });
    }
};
