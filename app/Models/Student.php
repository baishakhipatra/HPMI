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
        'image',
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
    // public static function generateStudentUid()
    // {
    //     do {
    //         // You can change the format as per your requirement
    //         $studentId = 'STU' . '-' . strtoupper(Str::random(4));
    //     } while (self::where('student_id', $studentId)->exists());

    //     return $studentId;
    // }
    public static function generateStudentUid($admissionYear, $classAlias, $rollNo)
    {
        // Format year
        $yearPart = substr($admissionYear, -2); // '2025' => '25'

        // Format roll number (pad to 2 digits)
        $rollPart = str_pad($rollNo, 2, '0', STR_PAD_LEFT);

        // Build ID
        $studentId = 'ST-' . $yearPart . strtoupper($classAlias) . $rollPart;

        // Ensure uniqueness
        $i = 1;
        $originalId = $studentId;
        while (self::where('student_id', $studentId)->exists()) {
            // Append counter if duplicate (e.g., ST-25V23-1)
            $studentId = $originalId . '-' . $i;
            $i++;
        }

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

    // Student.php
    public function marks()
    {
        return $this->hasMany(StudentsMark::class, 'student_id');
    }

    protected static function booted()
    {
        static::deleting(function ($student) {
            $student->marks()->delete();
        });
    }

}
