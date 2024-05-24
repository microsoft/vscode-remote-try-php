<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'subjects';
    protected $fillable = [
        'name',
        'faculty_id',
        'description'
    ];
    public function faculty(){
        return $this->belongsTo(Faculty::class, 'faculty_id', 'id');
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subject')->withPivot('point');
    }
}
