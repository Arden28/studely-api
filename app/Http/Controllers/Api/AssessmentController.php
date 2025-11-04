<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assessments\StoreAssessmentRequest;
use App\Http\Requests\Assessments\UpdateAssessmentRequest;
use App\Http\Resources\AssessmentResource;
use App\Models\{Assessment, Module};
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $r)
    {
        $tid = app('tenant.id');
        $q = Assessment::where('tenant_id',$tid)->withCount('questions')->latest();
        if ($r->filled('module_id')) $q->where('module_id',$r->module_id);
        return AssessmentResource::collection($q->paginate(20));
    }

    public function store(StoreAssessmentRequest $req)
    {
        $tid = app('tenant.id');
        $data = $req->validated();
        Module::where('tenant_id',$tid)->findOrFail($data['module_id']);
        $a = Assessment::create($data + ['tenant_id'=>$tid]);
        return new AssessmentResource($a);
    }

    public function show($id)
    {
        $tid = app('tenant.id');
        $a = Assessment::where('tenant_id',$tid)->with(['questions.options','rubric.criteria'])->findOrFail($id);
        return new AssessmentResource($a);
    }

    public function update(UpdateAssessmentRequest $req, $id)
    {
        $tid = app('tenant.id');
        $a = Assessment::where('tenant_id',$tid)->findOrFail($id);
        $a->update($req->validated());
        return new AssessmentResource($a);
    }

    public function destroy($id)
    {
        $tid = app('tenant.id');
        $a = Assessment::where('tenant_id',$tid)->findOrFail($id);
        $a->delete();
        return response()->json(['message'=>'deleted']);
    }
}

