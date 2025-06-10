<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;

if(!function_exists('generateTeacherId')) {
    function generateTeacherId() {
        do {
            // Count existing Admins and increment for next ID
            $count = Admin::count() + 1;

            // Generate ID in the format TCH0001, TCH0002, etc.
            $teacherId = 'EMP' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Check uniqueness in the database
            $exists = Admin::where('user_id', $teacherId)->exists();
        } while ($exists);

        return $teacherId;
    }
}
