<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubric extends Model
{
    use HasFactory;
    protected $fillable = ['assessment_id','title'];

    public function assessment(){ return $this->belongsTo(Assessment::class); }
    public function criteria(){ return $this->hasMany(RubricCriterion::class); }
}
