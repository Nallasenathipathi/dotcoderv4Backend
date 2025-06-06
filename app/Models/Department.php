<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['department_name','department_short_name','status','created_by','updated_by'];

    public function users(){
        return $this->belongsTo(User::class,'created_by','id');
    }
}
