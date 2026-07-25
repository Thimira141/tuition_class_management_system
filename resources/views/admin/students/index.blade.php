{{-- entry point for students area --}}

@extends('layouts.admin')

@section('title', 'Students')

@section('content-c1')

    <div class="container my-5">
        {{-- search box row --}}
        <div class="row py-2">
            <div class="col-md-8 col-sm-12 mb-3">
                <div class="input-group bg-body-secondary rounded shadow align-items-center">
                    <input type="text" class="form-control-lg bg-transparent border-0 focus-ring d-flex flex-fill"
                        placeholder="Search..." aria-label="Search...">
                    <button class="btn position-absolute end-0 h-100 bg-body-secondary" type="button"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-3">
                <div class="w-100 d-flex justify-content-center">
                    <button class="btn btn-lg btn-primary shadow" data-bs-toggle="modal" id="new-student-add-model-init-btn"
                        data-bs-target="#new-student-model"><i class="bi bi-person-plus me-1"></i>Add New Student</button>
                </div>
            </div>
        </div>
        {{-- data table --}}
        <div class="row"></div>
    </div>

    @include('admin.students.new')
    @include('admin.students.view')
@endsection

<script>
    const ROUTES = {
        'guardians_ts': "{{ route('students.ajax.ts.guardians-index') }}",
        'students_store': "{{ route('students.ajax.store') }}",
        'students_update': "{{ route('students.ajax.update', ':student_code') }}",
        'students_show': "{{ route('students.ajax.show', ':student_code') }}",
        'students_destroy': "{{ route('students.ajax.destroy', ':student_code') }}",
        'guardians_store': "{{ route('guardians.ajax.store') }}",
        'guardians_update': "{{ route('guardians.ajax.update', ':guardian_code') }}",
        'guardians_show': "{{ route('guardians.ajax.show', ':guardian_code') }}",
    }
</script>

@vite(['resources/js/students.js'])
