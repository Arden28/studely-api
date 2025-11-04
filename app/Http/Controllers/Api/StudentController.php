<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $r)
    {
        $tid = app('tenant.id');
        $paginated = Student::where('tenant_id',$tid)->latest()->paginate(20);
        return StudentResource::collection($paginated);
    }

    public function store(StoreStudentRequest $req)
    {
        $tid = app('tenant.id');
        $data = $req->validated() + ['tenant_id'=>$tid];
        $student = Student::create($data);
        return new StudentResource($student);
    }

    public function show($id)
    {
        $tid = app('tenant.id');
        $student = Student::where('tenant_id',$tid)->findOrFail($id);
        return new StudentResource($student);
    }

    public function update(UpdateStudentRequest $req, $id)
    {
        $tid = app('tenant.id');
        $student = Student::where('tenant_id',$tid)->findOrFail($id);
        $student->update($req->validated());
        return new StudentResource($student);
    }

    public function destroy($id)
    {
        $tid = app('tenant.id');
        $student = Student::where('tenant_id',$tid)->findOrFail($id);
        $student->delete();
        return response()->json(['message'=>'deleted']);
    }
}
