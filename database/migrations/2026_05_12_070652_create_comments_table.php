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
        Schema::create('grade_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('label');
            $table->enum('category', ['S', 'PSR', 'WSS', 'A', 'B', 'C', 'D', 'E', 'F']);
            $table->enum('type', ['midquarter', 'quarter']);
            $table->tinyInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['code', 'type']);
        });

        Schema::create('comments_midquarter', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->index();
            $table->integer('school_year_id');
            $table->integer('subject_id')->index();
            $table->tinyInteger('quarter');
            $table->foreignId('criteria_id')->constrained('grade_criteria')->cascadeOnDelete();
            $table->decimal('score', 4, 1)->default(0);
            $table->timestamps();

            $table->unique(['student_id', 'school_year_id', 'quarter', 'subject_id', 'criteria_id']);
        });

        Schema::create('comments_quarter', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->index();
            $table->integer('school_year_id');
            $table->foreignId('criteria_id')->constrained('grade_criteria')->cascadeOnDelete();
            $table->decimal('score', 4, 1)->default(0);
            $table->timestamps();

            $table->unique(['student_id', 'school_year_id', 'criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_criteria');
        Schema::dropIfExists('comments_midquarter');
        Schema::dropIfExists('comments_quarter');
    }
};
