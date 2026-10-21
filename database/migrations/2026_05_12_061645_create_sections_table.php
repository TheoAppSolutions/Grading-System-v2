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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->integer('grade_level_id');
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->string('label', 191);
            $table->tinyInteger('type')->nullable();
            $table->timestamps();

            $table->unique(['grade_level_id', 'label', 'school_year_id']);
        });

        Schema::create('student_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['student_id', 'school_year_id'],
                'student_sections_student_school_year_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_sections');
        Schema::dropIfExists('sections');
    }
};
