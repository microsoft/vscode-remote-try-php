<?php

namespace App\Repositories\Subject;

use App\Enums\Base;
use App\Models\Subject;
use App\Repositories\BaseRepository;

class SubjectRepository extends BaseRepository
{
    protected $subject;

    public function __construct(Subject $subject)
    {
        parent::__construct($subject);
    }

    public function subject($id){
        return $this->model->where('faculty_id',$id)->get();
    }


}
