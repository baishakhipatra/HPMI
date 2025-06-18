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
        'term_one_out_off',
        'term_one_stu_marks',
        'term_two_out_off',
        'term_two_stu_marks',
        'mid_term_out_off',
        'mid_term_stu_marks',
        'final_exam_out_off',
        'final_exam_stu_marks'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassList::class);
    }

    // public function subject()
    // {
    //     return $this->belongsTo(ClassWiseSubject::class);
    // }

    public function subjectlist() {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function studentAdmission()
    {
        return $this->belongsTo(StudentAdmission::class, 'student_admission_id');
    }
}
