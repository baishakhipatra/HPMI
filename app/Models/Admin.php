<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guard = 'admin';

    protected $fillable = ['teacher_id','name', 'user_name', 'user_type', 'status', 'email', 'mobile',
    'date_of_birth', 'date_of_joining','qualifications', 'subjects_taught', 'classes_assigned', 'password'];

    protected $hidden = ['password'];
}
