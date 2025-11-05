<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $r)
    {
        $tid = Auth::user()->tenant_id;
        // Log::info("Tenant: $tid");
        // abort_if(!$tid, 403, 'Tenant not resolved');

        $paginated = Student::where('tenant_id',$tid)->latest()->paginate(20);
        return StudentResource::collection($paginated);
    }

    public function store(StoreStudentRequest $req)
    {
        $tid = Auth::user()?->tenant_id;
        $data = $req->validated();

        // create user first
        $userData = $data['user'];
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'] ?? null,
            'tenant_id' => $tid,
            'password' => Str::password(12), // random; or send invite flow
        ]);

        // then create student linked to the user
        $student = Student::create([
            'tenant_id' => $tid,
            'user_id'   => $user->id,
            'reg_no'    => $data['reg_no'],
            'branch'    => $data['branch'] ?? null,
            'cohort'    => $data['cohort'] ?? null,
            'meta'      => $data['meta'] ?? null,
        ]);

        return new StudentResource($student->load('user'));
    }

    public function show($id)
    {
        $tid = Auth::user()?->tenant_id;
        $student = Student::where('tenant_id',$tid)->findOrFail($id);
        return new StudentResource($student);
    }

    public function update(UpdateStudentRequest $req, $id)
    {
        $tid = Auth::user()?->tenant_id;
        $student = Student::where('tenant_id',$tid)->findOrFail($id);
        $student->update($req->validated());
        return new StudentResource($student);
    }

    public function destroy($id)
    {
        $tid = Auth::user()?->tenant_id;
        $student = Student::where('tenant_id',$tid)->findOrFail($id);
        $student->delete();
        return response()->json(['message'=>'deleted']);
    }
}
