<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Http\Requests\UpdatePointRequest;
use App\Jobs\SendInfoUser;
use App\Models\Student;
use App\Repositories\Faculty\FacultyRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Student\StudentRepository;
use App\Repositories\Subject\SubjectRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StudentController extends Controller
{
    protected $facultyRepository;
    protected $studentRepository;
    protected $userRepository;
    protected $roleRepository;
    protected $subjectRepository;

    public function __construct(SubjectRepository $subjectRepository, FacultyRepository $facultyRepository, UserRepository $userRepository, StudentRepository $studentRepository, RoleRepository $roleRepository)
    {
        $this->facultyRepository = $facultyRepository;
        $this->userRepository = $userRepository;
        $this->studentRepository = $studentRepository;
        $this->roleRepository = $roleRepository;
        $this->subjectRepository = $subjectRepository;
    }

    public function index(Request $request)
    {
        $faculties = $this->facultyRepository->getList();
        $students = $this->studentRepository->filter($request);
        return view('student.index', compact('students', 'faculties'));
    }

    public function home()
    {
        return view('student.home');
    }

    public function create()
    {
        $faculties = $this->facultyRepository->getAll();
        return view('student.form', compact('faculties'));
    }

    public function store(StudentRequest $request)
    {
        DB::beginTransaction();
        try {
            $password = substr(md5(microtime()), rand(0, 26), 5);
            $dataUser = $request->all();
            $dataUser['password'] = $password;
            $dataUser['faculty_id'] = $request->faculty;
            if ($request->file('avatar')) {
                $dataUser['avatar'] = upLoadImage($request->file('avatar'), 'students');
            }
            $user = $this->userRepository->create($dataUser);
            $dataUser['id'] = $user->id;
            $this->studentRepository->create($dataUser);
            dispatch(new SendInfoUser($dataUser));
            $user->roles()->attach(2);
            DB::commit();
            if ($request->ajax()) {
                session()->flash('success', 'Add successful student');
                return response()->json([
                    'status' => true
                ]);
            }
            return redirect()->route('students.index')->with('success', 'Add successful student');
        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax()) {
                session()->flash('error', 'Add failed student');
                return response()->json([
                    'status' => false
                ]);
            }
            return redirect()->route('students.index')->with('error', 'Add failed student');
        }
    }

    public function edit($id)
    {
        $faculties = $this->facultyRepository->getAll();
        $student = $this->studentRepository->findOrFail($id);
        return view('student.form', compact('student', 'faculties'));
    }

    public function update(StudentRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $data = $request->all();
        if ($request->file('avatar')) {
            $data['avatar'] = upLoadImage($request->file('avatar'), 'students');
        }
        $data['faculty_id'] = $data['faculty'];
        $this->studentRepository->update($id, $data);
        $this->userRepository->update($id, $data);
        return redirect()->route('students.index')->with('success', 'Update successful student');
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $this->studentRepository->delete($id);
        return redirect()->back()->with('success', 'Delete successful student');
    }

    public function point(UpdatePointRequest $request): \Illuminate\Http\JsonResponse
    {
        $studentId = $request->student_id;
        $subjectId = $request->subject_id;
        $newPoint = $request->point;
        $student = $this->studentRepository->findOrFail($studentId);
        $data = $student->subjects()->where('subject_id', $subjectId)->where('student_id', $studentId)->count();
        if ($data) {
            $student = $this->studentRepository->findOrFail($studentId);
            $subjectId = $this->subjectRepository->findOrFail($subjectId);
            $student->subjects()->updateExistingPivot($subjectId, ['point' => $newPoint]);
            session()->flash('success', 'Update successful point');
            return response()->json([
                'status' => true,
            ]);
        } else {
            return response()->json([
                'status' => false,
            ]);
        }
    }
}
