<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Question, Option};
use Illuminate\Http\Request;

class QuestionOptionController extends Controller
{
    public function store(Request $r, $questionId)
    {
        $tid = app('tenant.id');
        $q = Question::where('tenant_id',$tid)->findOrFail($questionId);
        $data = $r->validate(['label'=>'required|string','is_correct'=>'boolean']);
        $opt = $q->options()->create($data);
        return response()->json($opt, 201);
    }

    public function destroy($id)
    {
        $opt = Option::with('question')->findOrFail($id);
        if (optional($opt->question)->tenant_id !== app('tenant.id')) abort(403);
        $opt->delete();
        return response()->json(['message'=>'deleted']);
    }
}

