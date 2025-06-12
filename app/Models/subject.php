<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class subject extends Model
{
    //
     use SoftDeletes;

    protected $table = 'subjects';

    protected $fillable = [
      'sub_name', 'sub_code', 'description', 'deleted_at'
    ];

     // Many-to-Many relationship with class wise subjects
    public function classWiseSubjects()
    {
        return $this->hasMany(ClassWiseSubject::class, 'subject_id');
    }
}
