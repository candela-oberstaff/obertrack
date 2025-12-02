<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkHoursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'work_date' => 'required|date',
            'hours_worked' => 'required|numeric|min:0|max:24',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'work_date.required' => 'La fecha de trabajo es obligatoria.',
            'work_date.date' => 'La fecha de trabajo debe ser una fecha válida.',
            'hours_worked.required' => 'Las horas trabajadas son obligatorias.',
            'hours_worked.numeric' => 'Las horas trabajadas deben ser un número.',
            'hours_worked.min' => 'Las horas trabajadas no pueden ser negativas.',
            'hours_worked.max' => 'No puedes registrar más de 24 horas en un día.',
        ];
    }
}
