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
        // classes table
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_code')->unique()->nullable();
            $table->string('name');
            $table->string('grade')->nullable();
            $table->string('remarks')->nullable();
            $table->enum('payment_method', ['once', 'monthly'])->default('monthly');
            $table->decimal('price', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->softDeletes();
            $table->timestamps();
        });

        // class_student pivot table
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->unique(['class_id', 'student_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
        Schema::dropIfExists('class_student');
    }
};
