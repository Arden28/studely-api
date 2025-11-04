<?php

// app/Http/Controllers/Api/EvaluationController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Evaluation\ScoreAttemptRequest;
use App\Http\Resources\CriterionScoreResource;
use App\Models\{Attempt, CriterionScore, RubricCriterion, Evaluator};
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function queue(Request $r)
    {
        $tid = app('tenant.id');
        $q = Attempt::where('tenant_id',$tid)
            ->whereNotNull('submitted_at')
            ->with('assessment','student')
            ->latest();

        return $q->paginate(20);
    }

    public function score(ScoreAttemptRequest $req, $attemptId)
    {
        $tid = app('tenant.id');
        $attempt = Attempt::where('tenant_id',$tid)->findOrFail($attemptId);

        $evaluator = Evaluator::firstOrCreate([
            'user_id' => $req->user()->id,
            'tenant_id' => $tid,
        ]);

        $resources = [];
        foreach ($req->validated()['scores'] as $row) {
            $crit = RubricCriterion::whereHas('rubric.assessment', fn($q)=>$q->where('tenant_id',$tid))
                ->findOrFail($row['criterion_id']);
            $score = CriterionScore::updateOrCreate(
                ['attempt_id'=>$attempt->id,'evaluator_id'=>$evaluator->id,'rubric_criterion_id'=>$crit->id],
                ['score'=>$row['score'],'comment'=>$row['comment'] ?? null]
            );
            $resources[] = new CriterionScoreResource($score->load(['criterion','evaluator.user']));
        }

        return $resources;
    }
}
