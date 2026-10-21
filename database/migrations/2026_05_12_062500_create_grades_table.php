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
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->integer('grade_level_id');
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->tinyInteger('quarter');
            $table->double('midquarter')->nullable();
            $table->double('final')->nullable();
            $table->double('effort')->nullable();
            $table->double('deportment')->nullable();
            $table->string('remarks', 191)->nullable();
            $table->timestamps();

            $table->index(['student_id', 'grade_level_id', 'school_year_id']);
            $table->index(['subject_id', 'grade_level_id', 'quarter']);
            $table->unique(
                ['student_id', 'subject_id', 'school_year_id', 'quarter'],
                'grades_student_subject_year_quarter_unique'
            );
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
