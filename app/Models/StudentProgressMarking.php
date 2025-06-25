<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgressMarking extends Model
{
   protected $table = 'student_progress_markings';
   protected $fillable = [
        'student_id', 'admission_session_id', 'progress_category', 'formative_first_phase', 'formative_second_phase', 'formative_third_phase', 'add_comments'
   ];
   public function pcategory(){
      return $this->hasMany(StudentProgressCategory::class, 'field', 'progress_category');
   }
}
