<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id','module_id','type','title','instructions','total_marks','is_active'
    ];
    protected $casts = ['is_active'=>'boolean'];

    public function tenant(){
         return $this->belongsTo(Tenant::class);
    }

    public function module(){
         return $this->belongsTo(Module::class);
    }

    public function questions(){
         return $this->hasMany(Question::class);
    }

    public function rubric(){
         return $this->hasOne(Rubric::class);
    }

    public function evaluators(){
         return $this->belongsToMany(Evaluator::class,'assessment_evaluators');
    }

    public function attempts(){
         return $this->hasMany(Attempt::class);
    }

}
