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
            $table->string('subject_id');
            $table->string('subject_label');
            $table->string('subject_title')->default('');
            $table->string('grade_level');
            $table->index('subject_id');
        });

        Schema::create('subject_users', function (Blueprint $table) {
            $table->id();
            $table->string('subject_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('user2_id')->nullable();
            $table->foreign('user2_id')->references('id')->on('users')->nullOnDelete();
            $table->string('school_year');
            $table->string('section')->default('');
            $table->string('semester')->default('');
            $table->string('category')->default('');
            $table->index('subject_id');
        });

        Schema::create('subject_units', function (Blueprint $table) {
            $table->id();
            $table->string('subject_id')->nullable();
            $table->float('units');
            $table->string('school_year')->nullable();
            $table->index('subject_id');
        });

        Schema::create('subject_excluded', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->index();
            $table->string('subject_id')->index();
            $table->string('school_year');
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
