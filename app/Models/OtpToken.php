<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel','identifier','purpose','code_hash','expires_at','attempts','consumed'
    ];
    protected $casts = ['expires_at'=>'datetime','consumed'=>'boolean'];
}
