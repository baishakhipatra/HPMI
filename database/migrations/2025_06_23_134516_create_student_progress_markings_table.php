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
        Schema::create('student_progress_markings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('admission_session_id');
            $table->string('progress_category')->nullable();
            $table->string('formative_first_phase')->nullable();
            $table->string('formative_second_phase')->nullable();
            $table->string('formative_third_phase')->nullable();
            $table->timestamps();

            $table->foreign('student_id')
            ->references('id')->on('students')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->foreign('admission_session_id')
            ->references('id')->on('student_admissions')
            ->onDelete('cascade')
            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress_markings');
    }
};
