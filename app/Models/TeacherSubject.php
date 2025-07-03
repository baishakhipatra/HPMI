<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    //
    protected $table = 'teacher_subjects';

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_id'
    ];

    /**
     * Get the class associated with this mapping.
     */
    public function subjectList() {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function classList() {
        return $this->belongsTo(ClassList::class, 'class_id');
    }
}
