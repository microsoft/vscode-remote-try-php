<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
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
        $subjectId = $this->route('subject');
        if($routeName == 'subjects.update'){
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('subjects')->ignore($subjectId, 'id')->where(function ($query) {
                        return $query->where('faculty_id', $this->faculty_id);
                    }),
                ],
                'faculty_id' => ['required', 'exists:faculties,id'],
                'description' => 'required|string|max:255',
            ];
        }else{
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('subjects')->where(function ($query) use ($subjectId) {
                        return $query->where('faculty_id', $this->faculty_id)
                            ->where('id', '<>', $subjectId);
                    }),
                ],
                'faculty_id' => ['required', 'exists:faculties,id'],
                'description' => 'required|string|max:255',
            ];
        }
    }
}
