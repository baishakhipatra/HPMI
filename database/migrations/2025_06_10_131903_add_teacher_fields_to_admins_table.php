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
        Schema::table('admins', function (Blueprint $table) {
            //
            $table->string('teacher_id')->unique()->nullable()->after('id');
            $table->date('date_of_birth')->nullable()->after('mobile');
            $table->date('date_of_joining')->nullable()->after('date_of_birth');
            $table->text('qualifications')->nullable()->after('date_of_joining');
            $table->text('subjects_taught')->nullable()->after('qualifications');
            $table->text('classes_assigned')->nullable()->after('subjects_taught');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            //
            $table->dropColumn([
                'teacher_id',
                'date_of_birth',
                'date_of_joining',
                'qualifications',
                'subjects_taught',
                'classes_assigned'
            ]);
        });
    }
};
