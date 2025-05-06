<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    //
    protected $fillable = ['lang_name', 'lang_id', 'lang_image', 'lang_category', 'status', 'created_by', 'updated_by'];
}
