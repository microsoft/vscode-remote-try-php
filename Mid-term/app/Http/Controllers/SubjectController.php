<?php

namespace App\Http\Controllers;

use App\Enums\Base;
use App\Http\Requests\ImportPointRequest;
use App\Http\Requests\ReisterRequest;
use App\Http\Requests\SubjectRequest;
use App\Http\Requests\UpdatePointRequest;
use App\Jobs\SendMailUnReigster;
use App\Repositories\Faculty\FacultyRepository;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Subject\SubjectRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SubjectController extends Controller
{
    protected $facultytRepository;
    protected $subjectRepository;
    protected $studentRepository;
    public function __construct(FacultyRepository $facultyRepository,SubjectRepository $subjectRepository,StudentRepository $studentRepository){
        $this->facultytRepository = $facultyRepository;
        $this->subjectRepository = $subjectRepository;
        $this->studentRepository = $studentRepository;
    }

    public function index()
    {
        $subjects = $this->subjectRepository->getList();
        foreach ($subjects as $subject){
            $subject['count'] = count($subject->students);
        }
        return view('subject.index',compact('subjects'));
    }
    public function create()
    {
        $faculties = $this->facultytRepository->getAll();
        return view('subject.form',compact('faculties'));
    }

    public function store(SubjectRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->subjectRepository->create($request->all());
        return redirect()->route('subjects.index')->with('success', 'Add successful subject');
    }

    public function edit($id)
    {
        $subject = $this->subjectRepository->findOrFail($id);
        $faculties = $this->facultytRepository->getAll();
        return view('subject.form', compact('subject','faculties'));


    }

    public function update(SubjectRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $this->subjectRepository->update($id,$request->all());
        return redirect()->route('subjects.index')->with('success', 'Update successful subject');
    }

    public function destroy($id)
    {
        $subjects = $this->subjectRepository->findOrFail($id);
        if(count($subjects->students()->get()) >0){
            return redirect()->back()->with('error', 'Deletion failed, students already registered for the subject');
        }
        $this->subjectRepository->delete($id);
        return redirect()->back()->with('success', 'Delete successful subject');
    }

    public function registerSubject(){
        $student = Auth::user()->student;
        $allSubject = $this->subjectRepository->subject($student->faculty_id);
        $isRegistered = $student->subjects;
        $subjectStatus = [];
        foreach ($allSubject as $subject) {
            $subjectStatus[] = [
                'subject' => $subject,
                'isRegistered' => $isRegistered->contains('id', $subject->id)
            ];
        }
        $perPage = Base::page;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $subjectStatusChunk = array_slice($subjectStatus, $offset, $perPage);
        $subjectStatusChunk = collect($subjectStatusChunk);
        $total = count($subjectStatus);
        $paginatedSubjectStatus = new LengthAwarePaginator(
            $subjectStatusChunk,
            $total,
            $perPage,
            $page
        );
        $paginatedSubjectStatus->setPath(URL::current());
        return view('subject.register_subject',compact('paginatedSubjectStatus'));
    }

    public function register(ReisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $student = Auth::user()->student;
        $checkedValues = $request->input('checkedValues');
        foreach ($checkedValues as $checkedValue) {
            $subject = $this->subjectRepository->findOrFail($checkedValue);
            if ($subject->faculty_id != $student->faculty_id) {
                return response()->json([
                    'status'=>false,
                ]);
            }
        }
        $student->subjects()->syncWithoutDetaching($checkedValues);
        session()->flash('success', 'Register successful subject');
        return response()->json([
            'status'=>true,
        ]);
    }

    public function result() {
        $student = Auth::user()->student;
        $this->subjectRepository->subject($student->faculty_id);
        return $this->extracted($student);
    }

    public function resultAll(){
        $page = request()->get('page', 1);
        $perPage = Base::page;
        $allStudents = $this->studentRepository->getAll();
        $paginatorInstance = new LengthAwarePaginator(
            $allStudents->forPage($page, $perPage)->values(),
            $allStudents->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
        $result = [];
        foreach ($paginatorInstance as $student) {
            $totalSubjects = $this->subjectRepository->subject($student->faculty_id);
            $registeredSubjectsCount = count($student->subjects);
            $totalSubjectsCount = count($totalSubjects);
            $unregisteredSubjectsCount = $totalSubjectsCount - $registeredSubjectsCount;
            $result[] = [
                'student' => $student,
                'reg' => $registeredSubjectsCount,
                'unreg' => $unregisteredSubjectsCount,
            ];
        }
//        dd($result);
        return view('admin.result',compact('result','paginatorInstance'));
    }

    public function unSubject($id,Request $request)
    {
        $student = $this->studentRepository->findOrFail($id);
        $totalSubjects = $this->subjectRepository->subject($student->faculty_id);
        $regSubjectIds = $student->subjects->pluck('id')->toArray();
        $unregisteredSubjects = $totalSubjects->whereNotIn('id', $regSubjectIds);
        if($request->ajax()){
            return response()->json([
                'data'=> $unregisteredSubjects
            ]);
        }else{
            return $unregisteredSubjects;
        }
    }

    public function send($id,Request $request): \Illuminate\Http\RedirectResponse
    {
        $student = $this->studentRepository->findOrFail($id);
        $data = [];
        $data['data'] = $this->unSubject($id,$request);
        $data['email'] = $student->user->email;
        dispatch(new SendMailUnReigster($data));
        return redirect()->route('resultAll')->with('success', 'Send successful email');
    }

    public function point($id){
       $student =  $this->studentRepository->findOrFail($id);
       return $this->extracted($student);
    }

    public function seenPoint($id,Request $request): \Illuminate\Http\JsonResponse
    {
        $student = $this->studentRepository->findOrFail($request->student_id);
        $data =  $student->subjects()->where('subject_id', $id)->first()->pivot;
        $infoSubject = $this->subjectRepository->findOrFail($id);
        return response()->json([
            'point'=> $data,
            'name' => $infoSubject->name,
        ]);
    }

    /**
     * @param $student
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function extracted($student)
    {
        $count = 0;
        $faculties =  $this->subjectRepository->subject($student->faculty_id);
        $sum = 0;
        $average = 0;
        foreach ($student->subjects as $subject) {
            if ($subject->pivot->point == null) {
                $count = -1;
                break;
            } else {
                $sum += $subject->pivot->point;
                $count++;
            }
        }
        if($count != 0){
            $average = $sum / $count;
        }
        return view('student.result', compact('student', 'average', 'faculties', 'count'));
    }

    public function change($id){
        $this->studentRepository->findOrFail($id);
        $student = $this->studentRepository->findOrFail($id);
        $subjects = $student->subjects;
        return view('subject.update-subjects',compact('subjects','student'));
    }

    public function pointSubjects($id): \Illuminate\Http\JsonResponse
    {
        $student = $this->studentRepository->findOrFail($id);
        $subjectsRegister = $student->subjects;
        return response()->json([
            'subjectsRegister' =>$subjectsRegister,
        ]);
    }

    public function updatePoints(UpdatePointRequest $request): \Illuminate\Http\JsonResponse
    {
        $student = $this->studentRepository->findOrFail($request->student_id);
        $subjectIds = $request->input('subject_id');
        $points = $request->input('point');
        $data = [];
        foreach ($subjectIds as $index => $subjectId) {
            $point = $points[$index];
            $data[$subjectId] = ['point' => $point];
        }
        $student->subjects()->sync($data);
        session()->flash('success', 'Update successful point');
        return response()->json([
            'status' => true
        ]);
    }

    public function import(ImportPointRequest $request): \Illuminate\Http\RedirectResponse
    {
        $the_file = $request->file('file_import');
        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $row_limit = $sheet->getHighestDataRow();
            $row_range = range(2, $row_limit);
            foreach ($row_range as $row) {
                $data = [
                    'student_id' => $sheet->getCell('A' . $row)->getValue(),
                    'subject_id' => $sheet->getCell('B' . $row)->getValue(),
                    'point' => $sheet->getCell('C' . $row)->getValue(),
                ];
                if($data['point'] >=0 && $data['point'] <=10){
                    $student = $this->studentRepository->findOrFail($data['student_id']);
                    $student->subjects()->syncWithoutDetaching([
                        $data['subject_id'] => ['point' => $data['point']]
                    ]);
                }else{
                    throw new \Exception();
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Update success point');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('error', 'Update false point');
        }

    }
}
