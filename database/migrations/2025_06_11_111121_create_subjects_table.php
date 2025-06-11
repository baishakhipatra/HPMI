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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('sub_name');
            $table->string('sub_code')->unique();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1: active | 0: inactive');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable(); // Adds deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
