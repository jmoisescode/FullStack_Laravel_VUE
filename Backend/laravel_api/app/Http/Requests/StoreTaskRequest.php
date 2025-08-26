<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *   schema="TaskRequest",
 *   type="object",
 *   required={"title", "status", "priority"},
 *   @OA\Property(property="title", type="string", example="Fix bug"),
 *   @OA\Property(property="description", type="string", example="Resolve cache issue"),
 *   @OA\Property(property="status", type="string", example="pending"),
 *   @OA\Property(property="priority", type="string", example="low")
 * )
 */
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
