<?php

namespace App\Http\Requests\Representative;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRepresentativeRequest extends FormRequest
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
            'name' =>['required','string','max:255',Rule::unique('tree4s','tree4_name')->ignore($this->representative->id,'id')],
            'email' =>['nullable' ,'email',Rule::unique('tree4s','email')->ignore($this->representative->id,'id')],
            'phone' => ['nullable',Rule::unique('tree4s','phone')->ignore($this->representative->id,'id')],
            'address' => 'nullable|string|max:255'
        ];
    }
}
