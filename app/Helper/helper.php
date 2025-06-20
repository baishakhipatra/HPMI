<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;

// if(!function_exists('generateEmployeeId')) {
//     function generateEmployeeId() {
//         do {
//             // Count existing Admins and increment for next ID
//             $count = Admin::count() + 1;

//             // Generate ID in the format TCH0001, TCH0002, etc.
//             $empId = 'EMP' . str_pad($count, 4, '0', STR_PAD_LEFT);

//             // Check uniqueness in the database
//             $exists = Admin::where('user_id', $empId)->exists();
//         } while ($exists);

//         return $empId;
//     }
// }

//for create employee ID
if (!function_exists('generateEmployeeId')) {
    function generateEmployeeId()
    {
       // Include soft-deleted records using withTrashed()
        $lastId = Admin::withTrashed()
            ->where('user_id', 'LIKE', 'EMP%')
            ->orderByRaw("CAST(SUBSTRING(user_id, 4) AS UNSIGNED) DESC")
            ->value('user_id');

        if ($lastId) {
            $number = (int) substr($lastId, 3); // Extract number after 'EMP'
            $nextNumber = $number + 1;
        } else {
            $nextNumber = 1;
        }

        // Return in EMP0001 format
        return 'EMP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}

//for create teacher ID
if (!function_exists('generateTeacherId')) {
    function generateTeacherId()
    {
        $lastId = Admin::withTrashed()
            ->where('user_id', 'LIKE', 'TEACH%')
            ->orderByRaw("CAST(SUBSTRING(user_id, 6) AS UNSIGNED) DESC") // Start from 6th char (after 'TEACH')
            ->value('user_id');

        if ($lastId) {
            $number = (int) substr($lastId, 5); // Extract numeric part
            $nextNumber = $number + 1;
        } else {
            $nextNumber = 1;
        }

        return 'TEACH' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}

function calculateGrade($total)
{
    if($total >= 90) return 'A+';
    if($total >= 80) return 'A';
    if($total >= 70) return 'B+';
    if($total >= 60) return 'B';
    if($total >= 50) return 'C';
    return 'F';
}