<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $fillable = ['college_name','college_short_name','status','created_by','updated_by'];
}
