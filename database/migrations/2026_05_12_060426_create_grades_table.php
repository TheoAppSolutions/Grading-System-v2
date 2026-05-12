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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject_id')->index();
            $table->tinyInteger('grade_level');
            $table->tinyInteger('quarter');
            $table->string('school_year')->default('0');
            $table->double('midquarter')->default(0);
            $table->double('final')->default(0);
            $table->double('effort')->default(0);
            $table->double('deportment')->default(0);
            $table->string('remarks', 191)->default('PASSED');
            $table->timestamps();

            $table->index(['student_id', 'grade_level', 'school_year']);
            $table->index(['subject_id', 'grade_level', 'quarter']);
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
