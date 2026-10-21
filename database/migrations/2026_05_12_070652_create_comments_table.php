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
        Schema::create('comments_criteria', function (Blueprint $table) {
            $table->id();
            $table->integer('comment_category_id');
            $table->string('code', 10);
            $table->string('description');
            $table->tinyInteger('type')->comment('1: midquarter, 2: quarter');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['code', 'type']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->json('comments_quarter');
            $table->json('comments_midquarter');
            $table->timestamps();

            $table->unique(['student_id', 'school_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments_criteria');
        Schema::dropIfExists('comments');
    }
};
