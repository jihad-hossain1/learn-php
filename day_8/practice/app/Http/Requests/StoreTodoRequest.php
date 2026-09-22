<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

enum TodoPriority: string 
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}

enum TodoStatus: string 
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Archived = 'archived';
    case Completed = 'completed';
}

class StoreTodoRequest extends FormRequest
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
            'title'=>['required','string','max:255'],
            'desc'=>['nullable','string'],
            // 'priority'=>['required','integer','min:1','max:5'],
            // 'email'=>['required','email',Rule::unique('users','email')]
            // 'status'=>['required',Rule::enum(TodoStatus::class)]
        ];
    }
}




