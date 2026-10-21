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
            $table->unsignedBigInteger('id')->autoIncrement(false)->primary();
            $table->string('student_lrn', 20)->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('gender');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

        Schema::create('student_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->date('birth_date')->nullable();
            $table->string('house_team')->nullable();
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->string('email_address')->nullable();
            $table->string('guardian')->nullable();
            $table->string('occupation')->nullable();
            $table->timestamps();
        });

        Schema::create('student_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
            $table->string('field_name')->nullable();
            $table->string('field_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_infos');
        Schema::dropIfExists('student_fields');
        Schema::dropIfExists('students');
    }
};
