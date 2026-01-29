<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'min:5', 
                'max:255', 
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\.]+(\s+[a-zA-ZáéíóúÁÉÍÓÚñÑ\.]+)+$/u', 
                'regex:/[aeiouAEIOUáéíóúÁÉÍÓÚ]/u'
            ],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone_number' => ['nullable', 'string', 'min:7', 'max:20', 'regex:/^\+?[0-9\s\-]+$/'],
            'location' => ['required', 'string', 'min:5', 'max:255'],
            'country' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u'],
            'city' => ['required', 'string', 'min:3', 'max:100', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u'],
            'company_name' => ['nullable', 'string', 'min:3', 'max:255'],
            'related_contact' => ['nullable', 'string', 'min:3', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }
}
