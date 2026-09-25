<?php

namespace App\Http\Requests;

use App\DTOs\TodoDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTodoRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'is_completed' => ['nullable', 'boolean'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'todo title',
            'description' => 'todo description',
            'priority' => 'priority level',
            'is_completed' => 'completion status',
            'due_date' => 'due date',
        ];
    }

    /**
     * Transform validated request data directly into a TodoDTO.
     */
    public function toDTO(): TodoDTO
    {
        $data = $this->validated();
        $data['is_completed'] = $this->boolean('is_completed');

        return TodoDTO::fromArray($data);
    }
}
