<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students_marks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('student_admission_id');

            $table->integer('term_one_out_off')->nullable();
            $table->integer('term_one_stu_marks')->nullable();
            $table->integer('term_two_out_off')->nullable();
            $table->integer('term_two_stu_marks')->nullable();
            $table->integer('mid_term_out_off')->nullable();
            $table->integer('mid_term_stu_marks')->nullable();
            $table->integer('final_exam_out_off')->nullable();
            $table->integer('final_exam_stu_marks')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('class_lists')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('student_admission_id')->references('id')->on('student_admissions')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_marks');
    }
};
