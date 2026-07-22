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
            $table->integer('grade_level_id');
            $table->integer('subject_category_id')->nullable();
            $table->string('subject_title');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('subject_users', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('user2_id')->nullable();
            $table->foreign('user2_id')->references('id')->on('users')->nullOnDelete();
            $table->integer('school_year_id');
            $table->integer('section_id')->nullable();
            $table->string('semester')->default('');
            $table->string('category')->default('');
            $table->index('subject_id');
            $table->timestamps();
        });

        Schema::create('subject_units', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id')->nullable();
            $table->string('school_year_id')->nullable();
            $table->index('subject_id');
            $table->float('units');
            $table->timestamps();
        });

        Schema::create('subject_excluded', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->index();
            $table->integer('subject_id')->index();
            $table->integer('school_year_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('subject_user');
        Schema::dropIfExists('subject_units');
        Schema::dropIfExists('subject_excluded');
    }
};
