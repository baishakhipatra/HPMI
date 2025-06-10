<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{

    use SoftDeletes;
    protected $table = 'students';

    protected $fillable = [
        'student_id',
        'student_name',
        'date_of_birth',
        'gender',
        'parent_name',
        'email',
        'phone_number',
        'address',
        'admission_date',
        'class',
        'section',
        'roll_number',
        'status'
    ];
}
