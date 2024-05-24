<?php

namespace App\Enums;


use Illuminate\Validation\Rules\Enum;

final class Base extends Enum
{
    const page = 10;
    const MALE = '0';
    const FEMALE = '1';
    const UNDEFINED = '2';

    public static function toSelectArray()
    {
        return [
            self::MALE => 'Male',
            self::FEMALE => 'Female',
            self::UNDEFINED => 'Unknown',
        ];
    }
}

