<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('students_marks', function (Blueprint $table) {
            
            if (Schema::hasColumn('students_marks', 'academic_year')) {
                $table->dropColumn('academic_year');
            }

            $table->unsignedBigInteger('student_admission_id')->after('subject_id');

            $table->foreign('student_admission_id')->references('id')->on('student_admissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('students_marks', function (Blueprint $table) {
            $table->dropForeign(['student_admission_id']);
            $table->dropColumn('student_admission_id');
            
            $table->string('academic_year')->nullable();
        });
    }
};
