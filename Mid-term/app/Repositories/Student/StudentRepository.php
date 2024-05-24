<?php

namespace App\Repositories\Student;

use App\Enums\Base;
use App\Models\Student;
use App\Repositories\BaseRepository;

class StudentRepository extends BaseRepository
{
    protected $student;


    public function __construct(Student $student)
    {
        parent::__construct($student);
    }

    public function filter($data): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $avgFrom = $data->avg_from;
        $avgTo = $data->avg_to;
        $minAge = $data->min_age;
        $maxAge = $data->max_age;

        return $this->model->with(['user','faculty','subjects' => function ($query) {
            $query->select('student_subject.student_id')
                ->selectRaw('AVG(student_subject.point) as average_point')
                ->join('subjects as subj', 'student_subject.subject_id', '=', 'subj.id')
                ->whereNotNull('student_subject.point')
                ->groupBy('student_subject.student_id')
                ->havingRaw('COUNT(subj.id) = (SELECT COUNT(*) FROM subjects WHERE faculty_id = (SELECT faculty_id FROM students WHERE id = student_subject.student_id) AND deleted_at IS NULL)');
        }])
        ->when($avgFrom != null, function ($query) use ($avgFrom) {
            $query->whereHas('subjects', function ($query) use ($avgFrom) {
                $query->havingRaw("AVG(student_subject.point) >= $avgFrom");
            });
        })
        ->when($avgTo != null, function ($query) use ($avgTo) {
            $query->whereHas('subjects', function ($query) use ($avgTo) {
                $query->havingRaw("AVG(student_subject.point) <= $avgTo");
            });
        })
        ->when($minAge != null, function ($query) use ($minAge) {
            $maxDate = now()->subYears($minAge)->toDateString();
            $query->where('birthday', '<=', $maxDate);
        })
        ->when($maxAge != null, function ($query) use ($maxAge) {
            $minDate = now()->subYears(intval($maxAge) + 1)->toDateString();
            $query->where('birthday', '>=', $minDate);
        })
        ->paginate(Base::page)->withQueryString();
    }
}
