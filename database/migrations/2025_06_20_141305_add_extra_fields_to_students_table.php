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
        Schema::table('students', function (Blueprint $table) {
            //
            $table->string('aadhar_no')->nullable()->after('student_id');
            $table->string('blood_group')->nullable()->after('aadhar_no');
            $table->decimal('height', 5, 2)->nullable()->after('blood_group'); // Example: 170.50 cm
            $table->decimal('weight', 5, 2)->nullable()->after('height'); // Example: 65.00 kg
            $table->string('father_name')->nullable()->after('parent_name');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->enum('divyang', ['Yes', 'No'])->default('No')->after('mother_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
            $table->dropColumn([
                'aadhar_no',
                'blood_group',
                'height',
                'weight',
                'father_name',
                'mother_name',
                'divyang'
            ]);
        });
    }
};
