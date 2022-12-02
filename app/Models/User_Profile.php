<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Profile extends Model
{
    use HasFactory;
    protected $table = 'user_profile';
    public $timestamps = false;
    protected $fillable = ['user_id','first_name','last_name','mobile_phone','address','country'];
}
