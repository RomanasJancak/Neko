<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user = $this->route('user');
        $userId = $user?->id;

        return [
            'user_name' => ['sometimes', 'required', 'string', 'max:255'],
            'user_email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'role' => ['sometimes', 'required', 'exists:roles,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
        ];
    }
}
