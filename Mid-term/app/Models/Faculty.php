<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'faculties';

    protected $fillable = [
        'name',
        'description',
    ];

    public function students(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class, 'id', 'faculty_id');
    }


}
