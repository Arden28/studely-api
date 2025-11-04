<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Questions\StoreQuestionRequest;
use App\Http\Requests\Questions\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\{Question, Assessment};
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $r)
    {
        $tid = app('tenant.id');
        $q = Question::where('tenant_id',$tid)->with('options')->latest();
        if ($r->filled('assessment_id')) $q->where('assessment_id',$r->assessment_id);
        return QuestionResource::collection($q->paginate(50));
    }

    public function store(StoreQuestionRequest $req)
    {
        $tid = app('tenant.id');
        $data = $req->validated();

        if (!empty($data['assessment_id'])) {
            Assessment::where('tenant_id',$tid)->findOrFail($data['assessment_id']);
        }

        $question = Question::create($data + ['tenant_id'=>$tid]);

        if ($question->type === 'MCQ' && !empty($data['options'])) {
            foreach ($data['options'] as $opt) {
                $question->options()->create([
                    'label' => $opt['label'],
                    'is_correct' => (bool)($opt['is_correct'] ?? false),
                ]);
            }
        }

        return new QuestionResource($question->load('options'));
    }

    public function show($id)
    {
        $tid = app('tenant.id');
        $q = Question::where('tenant_id',$tid)->with('options')->findOrFail($id);
        return new QuestionResource($q);
    }

    public function update(UpdateQuestionRequest $req, $id)
    {
        $tid = app('tenant.id');
        $q = Question::where('tenant_id',$tid)->findOrFail($id);
        $q->update($req->validated());
        return new QuestionResource($q->load('options'));
    }

    public function destroy($id)
    {
        $tid = app('tenant.id');
        $q = Question::where('tenant_id',$tid)->findOrFail($id);
        $q->delete();
        return response()->json(['message'=>'deleted']);
    }
}
