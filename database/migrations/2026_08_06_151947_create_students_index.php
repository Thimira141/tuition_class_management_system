<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index('student_code');   // speeds up searches on student code
            $table->index('name');   // speeds up searches on student name
            $table->index('tel');    // speeds up searches on telephone
            $table->index('dob');    // speeds up sorting/filtering by date of birth
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->index('guardian_code');   // speeds up searches on student code
            $table->index('name');   // speeds up searches on student name
            $table->index('nic');    // speeds up searches on telephone
            $table->index('tel');    // speeds up sorting/filtering by date of birth
        });

    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['student_code']);
            $table->dropIndex(['name']);
            $table->dropIndex(['tel']);
            $table->dropIndex(['dob']);
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->dropIndex(['guardian_code']);
            $table->dropIndex(['name']);
            $table->dropIndex(['nic']);
            $table->dropIndex(['tel']);
        });

    }
};
