<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware and policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_id' => 'sometimes|required|exists:projects,id',
            'status' => 'sometimes|in:todo,in_progress,completed,cancelled',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required.',
            'title.max' => 'The task title may not be greater than 255 characters.',
            'description.max' => 'The task description may not be greater than 1000 characters.',
            'project_id.required' => 'The project is required.',
            'project_id.exists' => 'The selected project does not exist.',
            'status.in' => 'The selected status is invalid. Valid options are: todo, in_progress, completed, cancelled.',
            'priority.in' => 'The selected priority is invalid. Valid options are: low, medium, high, urgent.',
        ];
    }

    /**
     * Custom validation to ensure user owns the project
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->project_id) {
                $project = \App\Models\Project::find($this->project_id);
                if ($project && $project->user_id !== \Illuminate\Support\Facades\Auth::id()) {
                    $validator->errors()->add('project_id', 'You can only assign tasks to your own projects.');
                }
            }
        });
    }
}
