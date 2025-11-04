<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    protected $fillable = ['name','code','status'];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function students(){
         return $this->hasMany(Student::class);
    }
}
