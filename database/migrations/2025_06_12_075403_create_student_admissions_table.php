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
        Schema::create('student_admissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
        $table->foreignId('session_id')->constrained('academic_sessions')->onDelete('cascade');
        $table->foreignId('class_id')->constrained('class_lists')->onDelete('cascade');
        $table->foreignId('section_id')->constrained('section_lists')->onDelete('cascade');
        $table->integer('roll_number');
        $table->date('admission_date');

        $table->unique(['student_id', 'session_id']); // 1 admission per session
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_admissions');
    }
};
