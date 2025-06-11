<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionList extends Model
{

    protected $fillable = ['section', 'class_list_id'];
    
    public function class()
    {
        return $this->belongsTo(ClassList::class, 'class_list_id');
    }
}
