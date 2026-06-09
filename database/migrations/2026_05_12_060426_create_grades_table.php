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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->index();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('subject_id')->index();
            $table->integer('grade_level_id');
            $table->integer('school_year_id');
            $table->tinyInteger('quarter');
            $table->double('midquarter')->nullable();
            $table->double('final')->nullable();
            $table->double('effort')->nullable();
            $table->double('deportment')->nullable();
            $table->string('remarks', 191)->nullable();
            $table->timestamps();

            $table->index(['student_id', 'grade_level_id', 'school_year_id']);
            $table->index(['subject_id', 'grade_level_id', 'quarter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
