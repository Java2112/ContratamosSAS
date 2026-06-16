<?php

namespace App\Domains\Selection\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Controlado por middleware/roles
    }

    public function rules(): bool|array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'anonymous_company' => 'boolean',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'positions_required' => 'required|integer|min:1',
            'priority' => 'required|string',
            
            'department' => 'nullable|string|max:255',
            'employer_type' => 'required|string|in:directa,contratamos',
            'contract_type' => 'required|string',
            'payroll_frequency' => 'required|string',
            'workday_type' => 'required|string',
            'schedule' => 'nullable|string|max:255',
            
            'salary_min' => 'required|numeric|min:0',
            'salary_max' => 'required|numeric|gte:salary_min',
            'has_bonuses' => 'boolean',
            'bonus_average' => 'nullable|numeric|min:0',
            
            'work_modality' => 'required|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'department_name' => 'nullable|string|max:255',
            
            'min_education_level' => 'required|string',
            'experience_value' => 'required|integer|min:0',
            'experience_unit' => 'required|string|in:meses,años',
            
            'languages' => 'nullable|array',
            'soft_skills' => 'nullable|array',
            'hard_skills' => 'nullable|array',
            
            'main_functions' => 'nullable|string',
            'optional_features' => 'nullable|string',
            'estimated_duration_months' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'salary_max.gte' => 'El salario máximo no puede ser menor al salario mínimo.',
            'experience_value.required' => 'La cantidad de experiencia es obligatoria.',
            'experience_unit.required' => 'La unidad de tiempo de experiencia (meses/años) es obligatoria.',
        ];
    }
}
