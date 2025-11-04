<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id','title','code','start_at','end_at','per_student_time_limit_min','order'
    ];
    protected $casts = [
        'start_at'=>'datetime',
        'end_at'=>'datetime'
    ];

    public function tenant(){
         return $this->belongsTo(Tenant::class);
    }

    public function assessments(){
         return $this->hasMany(Assessment::class);
    }

}
