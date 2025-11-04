<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id','user_id','reg_no','branch','cohort','meta'];
    protected $casts = ['meta'=>'array'];

    public function tenant(){ return $this->belongsTo(Tenant::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function attempts(){ return $this->hasMany(Attempt::class); }
}
