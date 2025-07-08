<?php

namespace App\Imports;

use App\Models\{Student, StudentAdmission};
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       // Create student first
        $student = Student::create([
            'student_name'  => $row['student_name'],         // maps from 'Student Name'
            'date_of_birth' => $row['date_of_birth'],
            'gender'        => $row['gender'],
            'aadhar_no'     => $row['aadhaar_number'],       // match exact Excel header (lowercase + underscores)
            'blood_group'   => $row['blood_group'],
            'height'        => $row['height'],
            'weight'        => $row['weight'],
            'father_name'   => $row['father_name'],
            'mother_name'   => $row['mother_name'],
            'parent_name'   => $row['parent_name'],
            'email'         => $row['email'],
            'phone_number'  => $row['phone_number'],
            'address'       => $row['address'],
            'divyang'       => $row['divyang'] ?? 'No',
        ]);

        // Then create admission
        StudentAdmission::create([
            'student_id'     => $student->id,
            'session_id'     => $row['session_id'],
            'class_id'       => $row['class_id'],
            'section_id'     => $row['section_id'],
            'roll_number'    => $row['roll_number'],
            'admission_date' => $row['admission_date'],
        ]);
    }
}
