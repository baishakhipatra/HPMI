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

}
