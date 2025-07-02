<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //

    public function designations()
    {
        return $this->belongsToMany(Designation::class, 'designation_permissions');
    }
}
