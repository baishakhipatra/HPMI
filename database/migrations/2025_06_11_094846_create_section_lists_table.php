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
        Schema::create('section_lists', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->unsignedBigInteger('class_list_id');
            $table->timestamps();

            $table->foreign('class_list_id')->references('id')->on('class_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_lists');
    }
};
