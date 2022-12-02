<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPassWord extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'password_resets';

    protected $fillable = ['id','email','token','expire_at','created_at'];
}
