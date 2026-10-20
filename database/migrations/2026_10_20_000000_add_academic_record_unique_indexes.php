<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->unique(
                ['student_id', 'subject_id', 'school_year_id', 'quarter'],
                'grades_student_subject_year_quarter_unique'
            );
        });

        Schema::table('subject_users', function (Blueprint $table) {
            $table->unique(
                ['subject_id', 'school_year_id'],
                'subject_users_subject_year_unique'
            );
        });

        Schema::table('subject_units', function (Blueprint $table) {
            $table->unique(
                ['subject_id', 'school_year_id'],
                'subject_units_subject_year_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropUnique('grades_student_subject_year_quarter_unique');
        });

        Schema::table('subject_users', function (Blueprint $table) {
            $table->dropUnique('subject_users_subject_year_unique');
        });

        Schema::table('subject_units', function (Blueprint $table) {
            $table->dropUnique('subject_units_subject_year_unique');
        });
    }
};
