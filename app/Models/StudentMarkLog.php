<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMarkLog extends Model
{
    //

    use SoftDeletes;
    protected $table = "student_mark_logs";

    protected $fillable = [
        'student_id',
        'updated_by',
        'message',
    ];
}
