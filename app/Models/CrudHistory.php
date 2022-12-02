<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrudHistory extends Model
{
    use HasFactory;
    protected $table = 'crud_history';
    protected $fillable = ['id','user_id','action','created_by_user','updated_by_user'];
}
