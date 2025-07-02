<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignationPermission extends Model
{
    //

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
