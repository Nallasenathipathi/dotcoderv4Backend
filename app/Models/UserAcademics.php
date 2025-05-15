<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAcademics extends Model
{
    //
    protected $fillable = ['user_id', 'college_id', 'batch_id', 'department_id', 'section_id', 'academic_marks', 'backlogs', 'status'];

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }
}
