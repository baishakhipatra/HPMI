<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentsMark extends Model
{
    use HasFactory;
    
    protected $table = "students_marks";

    protected $fillable = [
        'student_id',
        'class_id',
        'subject_id',
        'student_admission_id',
        'mid_term_out_off',
        'mid_term_stu_marks',
        'final_exam_out_off',
        'final_exam_stu_marks'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassList::class, 'class_id');
    }


    public function subjectlist() {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function studentAdmission()
    {
        return $this->belongsTo(StudentAdmission::class, 'student_admission_id')->with('session', 'class', 'student');
    }
}
