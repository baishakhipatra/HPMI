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
            $table->unsignedBigInteger('academic_session_id')->nullable()->after('id');

            $table->foreign('academic_session_id')
                ->references('id')
                ->on('academic_sessions')
                ->onDelete('cascade'); // optional: use 'set null' or 'restrict' if preferred
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
            $table->dropForeign(['academic_session_id']);
            $table->dropColumn('academic_session_id');
        });
    }
};
