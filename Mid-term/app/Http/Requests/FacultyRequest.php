<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;

class FacultyRequest extends FormRequest
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
        $facultyId = $this->route('faculty');
//        dd($routeName);
        if ($routeName == 'faculties.update') {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('faculties')->ignore($facultyId, 'id')
                ],
                'description' => 'required|string|max:255',
            ];
        }else {
            return [
                'name' => 'required|string|max:255|unique:faculties',
                'description' => 'required|string|max:255',
            ];
        }

    }

    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            redirect()->back()
                ->with('error', 'Thêm không thành công')
                ->withErrors($validator)
                ->withInput()
        );
    }
}
