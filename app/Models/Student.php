<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Student extends Model
{

    use SoftDeletes;
    protected $table = 'students';

    protected $fillable = [
        'student_id',
        'student_admission_id',
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
        'status',
        'aadhar_no',
        'blood_group',
        'height',
        'weight',
        'father_name',
        'mother_name',
        'divyang'

    ];

    public function admissions()
    {
        return $this->hasMany(StudentAdmission::class);
    }
    public static function generateStudentUid()
    {
        do {
            // You can change the format as per your requirement
            $studentId = 'STU-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (self::where('student_id', $studentId)->exists());

        return $studentId;
    }

    /*relationship with class_lists */
    public function class() {
        return $this->belongsTo(ClassList::class, 'class_id');
    }

    /*relationship with student_admissions */
    public function admission()
    {
        return $this->belongsTo(StudentAdmission::class, 'student_admission_id');
    }
}
