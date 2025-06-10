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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_id')->unique(); // Custom Teacher ID
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 20)->unique();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('subjects_taught')->nullable();
            $table->text('classes_assigned')->nullable();
            $table->string('role')->default('Teacher'); // e.g. Teacher, Coordinator, etc.
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
