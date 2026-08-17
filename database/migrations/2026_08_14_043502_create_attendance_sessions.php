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
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('attendance_session_code')->unique()->nullable();
            $table->string('title')->unique();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            // session_year and session_month both null means classes.payment_method = once
            $table->year('session_year')->nullable();
            $table->unsignedTinyInteger('session_month')->nullable(); // 1...12
            $table->timestamp('start_datetime')->nullable();
            $table->timestamp('end_datetime')->nullable();
            $table->timestamp('closed_at')->nullable(); // null = open, datetime = closed
            $table->softDeletes();
            $table->timestamps();
        });

        // pivot table for marking students
        Schema::create('students_attendance', function (Blueprint $table) {
            $table->foreignId('attendance_session_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->boolean('present')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('students_attendance');
    }
};
