<?php

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('student data table endpoint returns yajra datatable payload', function () {
    $user = User::factory()->create();

    $guardian = Guardian::create([
        'name' => 'John Doe',
        'nic' => '123456789V',
        'email' => 'guardian@example.com',
        'tel' => '0771234567',
        'address' => '123 Main Street',
        'remarks' => 'Test guardian',
    ]);

    Student::create([
        'name' => 'Jane Doe',
        'nic' => '987654321V',
        'dob' => '2010-01-01',
        'joined_at' => '2024-01-01',
        'email' => 'student@example.com',
        'tel' => '0712345678',
        'address' => '456 Main Street',
        'remarks' => 'Test student',
        'guardian_id' => $guardian->id,
    ]);

    $this->actingAs($user)
        ->getJson('/admin/students/ajax/dt-index')
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                ['student__student_code', 'student__name', 'student__cover_img'],
            ],
        ]);
});
