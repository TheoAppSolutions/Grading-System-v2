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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->unique();
            $table->string('student_lrn', 20)->nullable();
            $table->string('name');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('suffix')->default('');
            $table->string('section')->default('');
            $table->string('gender');
            $table->tinyInteger('active')->default(1);
        });

        Schema::create('student_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->unique();
            $table->date('birth_date')->nullable();
            $table->string('house_team')->nullable();
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->string('email_address')->nullable();
            $table->string('guardian')->nullable();
            $table->string('occupation')->nullable();
            $table->timestamps();
        });

        Schema::create('student_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->nullable()->index();
            $table->string('section')->nullable();
            $table->string('school_year')->nullable();
        });

        Schema::create('student_fields', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->nullable()->index();
            $table->string('field_name')->nullable();
            $table->string('field_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('student_infos');
        Schema::dropIfExists('student_sections');
        Schema::dropIfExists('student_fields');
    }
};
