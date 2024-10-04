<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:75',
            'position' => 'required|string|max:75',
            'image' => 'nullable|image|mimes:png,jpg,webp,jpeg|max:2048',
            'social' => 'nullable',
            'bio' => 'nullable',
        ];
    }
}
