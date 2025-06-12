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
}
