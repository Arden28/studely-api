<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriterionScore extends Model
{
    use HasFactory;
    protected $fillable = ['attempt_id','evaluator_id','rubric_criterion_id','score','comment'];

    public function attempt(){ return $this->belongsTo(Attempt::class); }
    public function evaluator(){ return $this->belongsTo(Evaluator::class); }
    public function criterion(){ return $this->belongsTo(RubricCriterion::class,'rubric_criterion_id'); }
}
