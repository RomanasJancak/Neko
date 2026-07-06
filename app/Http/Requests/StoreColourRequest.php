<?php

namespace App\Http\Requests;

use App\Models\Colour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreColourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'colour' => ['required', 'string', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'taskable_type' => ['required', 'string', Rule::in(Colour::taskableAliases())],
            'taskable_id' => ['required', 'integer', 'min:1'],
            'type' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $taskableClass = Colour::resolveTaskableClass($this->input('taskable_type'));

            if ($taskableClass === null) {
                return;
            }

            $taskable = new $taskableClass();

            if (!$taskable->newQuery()->whereKey($this->integer('taskable_id'))->exists()) {
                $validator->errors()->add('taskable_id', 'Selected taskable record does not exist.');
            }
        });
    }

    public function validatedPayload(): array
    {
        $validated = $this->validated();
        $validated['taskable_type'] = Colour::resolveTaskableClass($validated['taskable_type']);
        $validated['type'] = $validated['type'] ?? 'main';

        return $validated;
    }
}