<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialBank extends Model
{
    protected $fillable = ['course_id', 'topic_id', 'path', 'file_type', 'qb_type', 'status', 'created_by', 'updated_by'];
}
