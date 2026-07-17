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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('guardian_code')->unique();
            $table->string('cover_img', 256)->nullable();
            $table->string('name', 256);
            $table->string('nic', 100)->unique();
            $table->string('email', 256)->nullable();
            $table->string('tel', 20);
            $table->string('address', 256);
            $table->string('remarks', 256)->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_code')->unique();
            $table->string('cover_img', 256)->nullable();
            $table->string('name', 256);
            $table->string('nic', 100)->nullable()->unique();
            $table->date('dob');
            $table->date('joined_at')->default(now());
            $table->string('email', 256)->unique();
            $table->string('tel', 20);
            $table->string('address', 256);
            $table->string('remarks', 256)->nullable();
            $table->boolean('is_deleted')->default(false);

            // Foreign key to guardians
            $table->unsignedBigInteger('guardian_id');
            $table->foreign('guardian_id')->references('id')->on('guardians')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('guardian_student');
    }
};
