<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Or add logic for permissions
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', 'in:pending,completed'],
            'priority'    => ['required', 'in:low,medium,high'],
            'order'       => ['nullable', 'integer']
        ];
    }
}
