<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassList extends Model
{
    use SoftDeletes;
    protected $table = 'class_lists';

    protected $fillable = [
        'class',
        'status'
    ];

    public function sections()
    {
        return $this->hasMany(SectionList::class, 'class_list_id');
    }
}
