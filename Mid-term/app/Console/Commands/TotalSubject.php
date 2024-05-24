<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Mail;
class TotalSubject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'total:subject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

      $students = Student::with(['subjects' => function ($query) {
            $query->select('student_subject.student_id', DB::raw('AVG(student_subject.point) as average_point'))
                ->join('subjects as subj', 'student_subject.subject_id', '=', 'subj.id')
                ->whereNotNull('student_subject.point')
                ->groupBy('student_subject.student_id')
                ->havingRaw('COUNT(subj.id) = (SELECT COUNT(*) FROM subjects WHERE faculty_id = (SELECT faculty_id FROM students WHERE id = student_subject.student_id) AND deleted_at IS NULL)');
        }])
            ->when(true != null, function ($query) {
                $query->whereHas('subjects', function ($query) {
                    $query->havingRaw("AVG(student_subject.point) <= 5");
                });
            })->get();
      foreach ($students as $student){
          Student::find($student->id)->delete();
          Mail::to('20010865@st.phenikaa-uni.edu.vn')->send(new \App\Mail\ResultSubject(10));
      }
    }
}
