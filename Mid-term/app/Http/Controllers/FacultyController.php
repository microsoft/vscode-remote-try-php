<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacultyRequest;
use App\Repositories\Faculty\FacultyRepository;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    protected $facultytRepository;

    public function __construct(FacultyRepository $facultyRepository){
        $this->facultytRepository = $facultyRepository;
    }

    public function index()
    {
        $faculties =  $this->facultytRepository->getList();
        return view('faculty.index',compact('faculties'));

    }

    public function create()
    {
        return view('faculty.form', ['formType' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FacultyRequest $request)
    {
        $this->facultytRepository->create($request->except('_token'));
        return redirect()->route('faculties.index')->with('success', 'Add successful faculty');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $faculty = $this->facultytRepository->findOrFail($id);
        return view('faculty.form', ['faculty' => $faculty, 'formType' => 'edit']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(FacultyRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        $this->facultytRepository->update($id,$request->except(['_token', '_method']));
        return redirect()->route('faculties.index')->with('success', 'Update successful faculty');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $faculty =  $this->facultytRepository->findOrFail($id);
        if(count($faculty->students()->get()) > 0){
            return redirect()->back()->with('error', 'Failed to delete faculty, There are already students registered for this faculty');
        }
        $this->facultytRepository->delete($id);
        return redirect()->back()->with('success', 'Delete successful faculty');
    }
}
