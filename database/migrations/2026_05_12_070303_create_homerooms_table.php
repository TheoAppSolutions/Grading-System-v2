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
        Schema::create('homerooms', function (Blueprint $table) {
            $table->id();
            $table->integer('grade_level_id');
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->integer('section_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('user2_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homerooms');
    }
};
