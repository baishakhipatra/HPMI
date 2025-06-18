<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAdmission extends Model
{
    protected $table = 'Student_admissions';

    protected $fillable = [
         'student_id', 'session_id', 'class_id', 'section', 'roll_number', 'admission_date'
    ];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassList::class, 'class_id');
    }
    public function section()
    {
        return $this->belongsTo(SectionList::class);
    }

    public function academicsession()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    

}
