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
        Schema::table('student_related_tables', function (Blueprint $table) {
            //

            Schema::table('students_marks', function (Blueprint $table) {
                $table->softDeletes();
            });

            Schema::table('student_admissions', function (Blueprint $table) {
                $table->softDeletes();
            });

            Schema::table('student_mark_logs', function (Blueprint $table) {
                $table->softDeletes();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_related_tables', function (Blueprint $table) {
            //
        });
    }
};
