<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guard = 'admin';

    protected $fillable = ['name', 'user_id', 'user_name', 'user_type', 'status', 'email', 'mobile', 'address',
    'date_of_birth', 'date_of_joining','qualifications', 'subjects_taught', 'classes_assigned', 'password'];

    protected $hidden = ['password'];

    public function class()
    {
        return $this->belongsTo(ClassList::class, 'classes_assigned');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subjects_taught');
    }
}
