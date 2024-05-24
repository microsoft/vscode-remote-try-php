<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterValuesExist implements Rule
{

    public function __construct()
    {
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $values)
    {
        $exists = false;
        foreach ($values as $value) {
            $facultyId = Auth::user()->student->faculty_id;
            $exists = DB::table('subjects')
                ->where('id', $value)
                ->where('faculty_id', $facultyId)
                ->exists();
        }
        dd($exists);
//        return true;
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
