<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id','assessment_id','type','stem','difficulty','topic','tags'
    ];
    protected $casts = [
        'tags'=>'array'
    ];

    public function assessment(){
         return $this->belongsTo(Assessment::class);
    }

    public function options(){
         return $this->hasMany(Option::class);
    }

}
