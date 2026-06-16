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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('website')->nullable()->after('email');
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->boolean('anonymous_company')->default(false)->after('client_id');
            
            // Renaming salary fields to match requested names
            $table->renameColumn('min_salary', 'salary_min');
            $table->renameColumn('max_salary', 'salary_max');
            
            // Experience fields
            $table->integer('experience_value')->nullable()->after('min_education_level');
            $table->string('experience_unit')->nullable()->after('experience_value'); // meses, años
        });

        // Data migration for experience if applicable
        \DB::table('vacancies')->whereNotNull('min_experience_years')->get()->each(function ($vacancy) {
            \DB::table('vacancies')->where('id', $vacancy->id)->update([
                'experience_value' => $vacancy->min_experience_years,
                'experience_unit' => 'años'
            ]);
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn('min_experience_years');
            $table->dropColumn('salary_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('website');
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn(['anonymous_company', 'experience_value', 'experience_unit']);
            $table->renameColumn('salary_min', 'min_salary');
            $table->renameColumn('salary_max', 'max_salary');
            $table->integer('min_experience_years')->nullable();
            $table->string('salary_type')->nullable();
        });
    }
};
