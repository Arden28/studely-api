<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'module_id','type','stem','difficulty','topic','tags', 'points'
    ];
    protected $casts = [
        'tags'=>'array',
        'points'=>'integer'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function options(){
         return $this->hasMany(Option::class);
    }

}
