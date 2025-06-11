<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateClassWiseSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_wise_subjects', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('class_id')->comment('Foreign key referencing class_lists.id');
            $table->unsignedBigInteger('subject_id')->comment('Foreign key referencing subjects.id');

            $table->timestamps();

            $table->foreign('class_id', 'fk_classwise_class')
                  ->references('id')->on('class_lists')
                  ->onDelete('cascade');

            $table->foreign('subject_id', 'fk_classwise_subject')
                  ->references('id')->on('subjects')
                  ->onDelete('cascade');

            $table->unique(['class_id', 'subject_id'], 'unique_class_subject'); // Prevent duplicate mappings
        });

        // Add table-level comment
        DB::statement("ALTER TABLE class_wise_subjects COMMENT = 'Pivot table for mapping classes to subjects'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_wise_subjects');
    }
}
