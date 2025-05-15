<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QbCourse extends Model
{
    protected $fillable = ['course_name', 'status', 'created_by', 'updated_by'];

    public function topics()
    {
        return $this->hasMany(QbTopics::class, 'course_id');
    }
}
