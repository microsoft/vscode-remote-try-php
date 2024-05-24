<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $routeName = $this->route()->getName();
        $studentId = $this->route('student');
        $rules = [
            'name' => 'required|string|max:255|unique:users',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|min:10|max:255|regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/|unique:users',
            'gender' => ['required', 'in:' . implode(',', array_keys(\App\Enums\Base::toSelectArray()))],
            'faculty' => ['required', 'exists:faculties,id'],
            'birthday' => 'required|date',
            'address' => 'required|string|max:255',
            'phone' => ['required', 'regex:/^\+?[0-9]{8,}$/'],
            'avatar' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048'
        ];
        if ($routeName == 'students.update') {
            $rules['name'] = ['required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($studentId, 'id')];
            $rules['email'] = ['required', 'email', 'min:10', 'max:255', 'regex:/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                Rule::unique('users')->ignore($studentId, 'id')];
        }
        return $rules;
    }
}
