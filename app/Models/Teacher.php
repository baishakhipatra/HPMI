<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    //
    use Notifiable, SoftDeletes;

    protected $table = 'teachers';

    protected $fillable = ['teacher_id', 'name', 'email', 'phone', 'date_of_birth', 'date_of_joining',
     'qualifications', 'subjects_taught', 'classes_assigned', 'role'];

}
