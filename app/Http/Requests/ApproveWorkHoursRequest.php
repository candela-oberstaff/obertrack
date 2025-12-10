<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveWorkHoursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Empleadores y Managers pueden aprobar horas
        return auth()->check() && (auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'week_start' => 'required|date',
            'employee_id' => 'required|exists:users,id',
            'comment' => 'nullable|string|max:255',
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
            'week_start.required' => 'La fecha de inicio de semana es obligatoria.',
            'week_start.date' => 'La fecha de inicio debe ser una fecha válida.',
            'employee_id.required' => 'El ID del empleado es obligatorio.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'comment.max' => 'El comentario no puede exceder 255 caracteres.',
        ];
    }
}
