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
        Schema::table('students_marks', function (Blueprint $table) {
            $table->dropColumn([
                'term_one_out_off',
                'term_one_stu_marks',
                'term_two_out_off',
                'term_two_stu_marks',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students_marks', function (Blueprint $table) {
            $table->integer('term_one_out_off')->nullable();
            $table->integer('term_one_stu_marks')->nullable();
            $table->integer('term_two_out_off')->nullable();
            $table->integer('term_two_stu_marks')->nullable();
        });
    }
};
