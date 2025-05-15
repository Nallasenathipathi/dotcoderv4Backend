<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QbTopics extends Model
{
    protected $fillable = ['course_id', 'topic_tag_id', 'topic_name', 'status', 'created_by', 'updated_by'];

    public function course()
    {
        return $this->belongsTo(QbCourse::class, 'course_id');
    }
}
