<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        // Insert sample data
        DB::table('designations')->insert([
            ['name' => 'Teacher', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Employee', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
     

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
