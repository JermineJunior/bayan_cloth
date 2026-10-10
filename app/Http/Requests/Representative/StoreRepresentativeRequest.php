<?php

namespace App\Http\Requests\Representative;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepresentativeRequest extends FormRequest
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
            'name' =>'required|string|max:255|unique:tree4s,tree4_name',
            'email' =>'nullable|email|unique:tree4s,email',
            'address' =>'nullable|string|max:255',
            'phone' =>'nullable|unique:tree4s,phone',
            'rep_percentage' => 'nullable|numeric|between:0,100',
        ];
    }
}
