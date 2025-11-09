<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id','user_id', 'college_id','reg_no','branch','cohort','meta', 'institution_name', 'university_name', 'gender', 'dob', 'admission_year', 'current_semester'];
    protected $casts = ['meta'=>'array'];

    public function tenant(){ return $this->belongsTo(Tenant::class); }

    public function user(){ return $this->belongsTo(User::class); }

    public function college(){
         return $this->belongsTo(College::class);
    }

    public function attempts(){ return $this->hasMany(Attempt::class); }
}
