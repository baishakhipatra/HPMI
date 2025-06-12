<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    //

    //relation with students
    public function students()
    {
        return $this->hasMany(Student::class, 'academic_session_id');
    }
}
