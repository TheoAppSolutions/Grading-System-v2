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
            $table->string('subject_code')->nullable()->unique();
            $table->integer('subject_category_id')->nullable();
            $table->string('subject_title');
            $table->integer('grade_level_id');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('subject_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('user2_id')->nullable();
            $table->foreign('user2_id')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('semester')->default('');
            $table->string('category')->default('');
            $table->index('subject_id');
            $table->unique(
                ['subject_id', 'school_year_id'],
                'subject_users_subject_year_unique'
            );
            $table->timestamps();
        });

        Schema::create('subject_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->index('subject_id');
            $table->unique(
                ['subject_id', 'school_year_id'],
                'subject_units_subject_year_unique'
            );
            $table->float('units');
            $table->timestamps();
        });

        Schema::create('subject_excluded', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_excluded');
        Schema::dropIfExists('subject_units');
        Schema::dropIfExists('subject_users');
        Schema::dropIfExists('subjects');
    }
};
