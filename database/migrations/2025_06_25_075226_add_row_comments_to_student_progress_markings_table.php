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
        Schema::table('student_progress_markings', function (Blueprint $table) {
            //
            $table->text('add_comments')->nullable()->after('formative_third_phase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_progress_markings', function (Blueprint $table) {
            //
            $table->dropColumn('add_comments');
        });
    }
};
