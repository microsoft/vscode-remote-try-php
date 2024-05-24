<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Student extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'students';
    protected $fillable = [
        'id',
        'faculty_id',
        'full_name',
        'birthday',
        'phone',
        'gender',
        'avatar',
        'address'
    ];
    public function user(){
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function faculty(){
        return $this->belongsTo(Faculty::class, 'faculty_id', 'id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject')->withPivot('point');
    }
    public function getAgeAttribute()
    {
        return now()->diffInYears($this->attributes['birthday']);
    }

}
