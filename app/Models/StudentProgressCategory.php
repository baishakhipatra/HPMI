<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class StudentProgressCategory extends Model
{
    use HasFactory;
    protected $table = ' student_progress_categories';

    protected $fillable = [
        'field',
        'value',
        'status'
    ];
}
