<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCouriersWithWorkloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
