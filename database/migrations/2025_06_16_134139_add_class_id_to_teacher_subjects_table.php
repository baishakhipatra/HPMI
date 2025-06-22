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
           Schema::table('teacher_subjects', function (Blueprint $table) {
                $table->unsignedBigInteger('class_id')->nullable()->after('subject_id');
            });

            // Add foreign key constraint after fixing data
            DB::statement('
                ALTER TABLE teacher_subjects
                ADD CONSTRAINT teacher_subjects_class_id_foreign
                FOREIGN KEY (class_id) REFERENCES class_lists(id)
                ON DELETE CASCADE ON UPDATE CASCADE
            ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            //
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
