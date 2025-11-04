<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubricCriterion extends Model
{
    use HasFactory;
    protected $fillable = ['rubric_id','name','weight','max_score'];

    public function rubric(){ return $this->belongsTo(Rubric::class); }
}
