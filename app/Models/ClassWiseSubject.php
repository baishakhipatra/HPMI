<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassWiseSubject extends Model
{
    //

    protected $table = 'class_wise_subjects';

    protected $fillable = [
        'class_id',
        'subject_id',
    ];

    /**
     * Get the class associated with this mapping.
     */
    public function classList(): BelongsTo
    {
        return $this->belongsTo(ClassList::class, 'class_id');
    }

    /**
     * Get the subject associated with this mapping.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
