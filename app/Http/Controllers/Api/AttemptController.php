<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attempts\SaveProgressRequest;
use App\Http\Resources\AttemptResource;
use App\Models\{Assessment, Attempt, Question, Response, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttemptController extends Controller
{
    public function start(Request $r, $assessmentId)
    {
        $user = User::find(Auth::user()->id);
        $tid = $user->tenant_id;
        $student = $user->student;
        $assessment = Assessment::where('tenant_id',$tid)->findOrFail($assessmentId);
        Log::info("Assessment: $assessment");

        $attempt = Attempt::firstOrCreate(
            [
                'tenant_id'=>$tid,
                'assessment_id'=>$assessment->id,
                'student_id'=>$student->id
            ],
            ['started_at'=>now()]
        );

        return new AttemptResource($attempt->load('assessment'));
    }

    public function saveProgress(SaveProgressRequest $req, $attemptId)
    {
        $tid = app('tenant.id');
        $data = $req->validated();

        $attempt = Attempt::where('tenant_id',$tid)->findOrFail($attemptId);
        $q = Question::where('tenant_id',$tid)->findOrFail($data['question_id']);
        if ($q->assessment_id && $q->assessment_id !== $attempt->assessment_id) abort(403);

        Response::updateOrCreate(
            ['attempt_id'=>$attempt->id,'question_id'=>$q->id],
            ['option_id'=>$data['option_id'] ?? null, 'text_answer'=>$data['text_answer'] ?? null]
        );

        return response()->json(['message'=>'saved']);
    }

    public function submit(Request $r, $attemptId)
    {
        $tid = app('tenant.id');
        $attempt = Attempt::where('tenant_id',$tid)->with('responses.question','assessment')->findOrFail($attemptId);

        DB::transaction(function() use ($attempt) {
            $score = 0;
            foreach ($attempt->responses as $resp) {
                if ($resp->question->type === 'MCQ' && $resp->option && $resp->option->is_correct) {
                    $score += 1;
                }
            }
            $attempt->score = $score;
            $attempt->submitted_at = now();
            $attempt->save();
        });

        return new AttemptResource($attempt->fresh()->load('assessment'));
    }
}
