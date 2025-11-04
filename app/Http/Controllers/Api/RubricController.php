<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rubrics\StoreRubricRequest;
use App\Http\Requests\Rubrics\UpdateRubricRequest;
use App\Http\Requests\Rubrics\StoreCriterionRequest;
use App\Http\Requests\Rubrics\UpdateCriterionRequest;
use App\Http\Resources\RubricResource;
use App\Http\Resources\RubricCriterionResource;
use App\Models\{Rubric, RubricCriterion, Assessment};
use Illuminate\Http\Request;

class RubricController extends Controller
{
    public function index(Request $r)
    {
        $tid = app('tenant.id');
        $q = Rubric::whereHas('assessment', fn($qq)=>$qq->where('tenant_id',$tid))
            ->with('criteria')
            ->latest();
        if ($r->filled('assessment_id')) $q->where('assessment_id', $r->assessment_id);
        return RubricResource::collection($q->paginate(20));
    }

    public function store(StoreRubricRequest $req)
    {
        $tid = app('tenant.id');
        $data = $req->validated();
        Assessment::where('tenant_id',$tid)->findOrFail($data['assessment_id']);

        $rubric = Rubric::create([
            'assessment_id' => $data['assessment_id'],
            'title' => $data['title'],
        ]);

        if (!empty($data['criteria'])) {
            foreach ($data['criteria'] as $c) {
                $rubric->criteria()->create($c);
            }
        }
        return new RubricResource($rubric->load('criteria'));
    }

    public function show($id)
    {
        $tid = app('tenant.id');
        $rubric = Rubric::with('criteria')
            ->whereHas('assessment', fn($q)=>$q->where('tenant_id',$tid))
            ->findOrFail($id);
        return new RubricResource($rubric);
    }

    public function update(UpdateRubricRequest $req, $id)
    {
        $tid = app('tenant.id');
        $rubric = Rubric::whereHas('assessment', fn($q)=>$q->where('tenant_id',$tid))->findOrFail($id);
        $rubric->update($req->validated());
        return new RubricResource($rubric->load('criteria'));
    }

    public function destroy($id)
    {
        $tid = app('tenant.id');
        $rubric = Rubric::whereHas('assessment', fn($q)=>$q->where('tenant_id',$tid))->findOrFail($id);
        $rubric->delete();
        return response()->json(['message'=>'deleted']);
    }

    // Criteria endpoints
    public function storeCriterion(StoreCriterionRequest $req)
    {
        $tid = app('tenant.id');
        $data = $req->validated();

        $rubric = Rubric::whereHas('assessment', fn($q)=>$q->where('tenant_id',$tid))
            ->findOrFail($data['rubric_id']);

        $criterion = $rubric->criteria()->create($data);
        return new RubricCriterionResource($criterion);
    }

    public function updateCriterion(UpdateCriterionRequest $req, $id)
    {
        $tid = app('tenant.id');
        $criterion = RubricCriterion::whereHas('rubric.assessment', fn($q)=>$q->where('tenant_id',$tid))
            ->findOrFail($id);
        $criterion->update($req->validated());
        return new RubricCriterionResource($criterion);
    }

    public function destroyCriterion($id)
    {
        $tid = app('tenant.id');
        $criterion = RubricCriterion::whereHas('rubric.assessment', fn($q)=>$q->where('tenant_id',$tid))
            ->findOrFail($id);
        $criterion->delete();
        return response()->json(['message'=>'deleted']);
    }
}
