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
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('school_year');
            $table->string('school_year_start');
            $table->string('school_year_end');
            $table->tinyInteger('active')->default(0);
            $table->integer('attendance_entries')->default(10);
            $table->string('card_template')->nullable();
            $table->timestamps();
        });

        Schema::create('school_year_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->onDelete('cascade');
            $table->tinyInteger('month');
            $table->unsignedInteger('days')->default(0);
            $table->timestamps();

            $table->unique(['school_year_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_years');
        Schema::dropIfExists('school_year_infos');
    }
};
