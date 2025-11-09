<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'code', 'location', 'description'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
