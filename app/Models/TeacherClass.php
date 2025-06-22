<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherClass extends Model
{
    //
    protected $table = 'teacher_classes';

    protected $fillable = [
        'teacher_id',
        'class_id',
    ];

    /**
     * Get the class associated with this mapping.
     */
    public function classList() {
        return $this->belongsTo(ClassList::class, 'class_id');
    }
}
