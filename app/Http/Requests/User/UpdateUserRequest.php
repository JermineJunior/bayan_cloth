<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|string|max:225',
            'email' => [Rule::unique('users','email')->ignore($this->user->id, 'id')],
            'phone_number' => [Rule::unique('users','phone_number')->ignore($this->user->id, 'id')],
            'password' => 'nullable|string|confirmed',
            'roles_name' => 'required|numeric|exists:roles,id'

        ];
    }
}
