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
        Schema::create('deportments', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id')->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('school_year_id');
            $table->float('deportment')->nullable();
            $table->tinyInteger('quarter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deportments');
    }
};
