<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class UpdatePointRequest extends FormRequest
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
        if($routeName == 'points.update'){
           return [
               'student_id' => 'required|exists:students,id',
               'subject_id' => ['required', 'array', Rule::exists('subjects', 'id')],
               'subject_id.*' => Rule::exists('subjects', 'id'),
               'point' => ['required', 'array', 'min:' . count($this->input('subject_id')), 'max:' . count($this->input('subject_id'))],
               'point.*' => 'required|numeric|min:0|max:10',
           ];
        }
        return [
            'student_id' => 'required|exists:student_subject,student_id',
            'subject_id' => 'required|exists:student_subject,subject_id',
            'point' => 'required|numeric|between:0,10',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                Response::json([
                    'errors' => $validator->errors(),
                ])
            );
        }
    }
}
