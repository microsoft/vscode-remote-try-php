<?php

namespace App\Repositories\Faculty;

use App\Models\Faculty;
use App\Repositories\BaseRepository;

class FacultyRepository extends BaseRepository
{
    protected $faculty;

    public function __construct(Faculty $faculty)
    {
        parent::__construct($faculty);
    }
}
