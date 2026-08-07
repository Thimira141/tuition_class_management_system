{{-- entry point for classrooms area --}}

@extends('layouts.admin')

@section('title', 'classrooms')

@section('content-c1')

    <div class="container my-5">
        {{-- search box row --}}
        {{-- <div class="row py-2 d-none"> <!-- remove this row, handle by dt -->
            <div class="col-md-8 col-sm-12 mb-3">
                <div class="input-group bg-body-secondary rounded shadow align-items-center">
                    <input id="dt-classroom-index-q-input" type="text" class="form-control-lg bg-transparent border-0 focus-ring d-flex flex-fill"
                        placeholder="Search..." aria-label="Search...">
                    <button class="btn position-absolute end-0 h-100 bg-body-secondary pe-none" type="button"><i
                            class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-3">
                <div class="w-100 d-flex justify-content-center">
                    <button class="btn btn-lg btn-primary shadow" data-bs-toggle="modal" id="new-classroom-add-model-init-btn"
                        data-bs-target="#new-classroom-model"><i class="bi bi-person-plus me-1"></i>Add New classroom</button>
                </div>
            </div>
        </div> --}}
        <p>classrooms index</p>
        {{-- data table --}}
        <div class="row rounded shadow">
            <div class="table-responsive">
                <table id="dt-classrooms-index-table" class="table table-hover m-0 rounded">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Class</th>
                            <th scope="col">Class Code</th>
                            <th scope="col">Grade</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>

    @include('admin.classrooms.new')
    @include('admin.classrooms.view')
    @include('admin.classrooms.students')
@endsection

<script>
    const ROUTES = {
        'classrooms_dt_index': "{{ route('classrooms.ajax.dt.index') }}",
        'classrooms_store': "{{ route('classrooms.ajax.store') }}",
        'classrooms_update': "{{ route('classrooms.ajax.update', ':classroom_code') }}",
        'classrooms_show': "{{ route('classrooms.ajax.show', ':classroom_code') }}",
        'classrooms_destroy': "{{ route('classrooms.ajax.destroy', ':classroom_code') }}",
        'classroom_students_dt_index': "{{ route('classrooms.student.ajax.dt.index', ':classroom') }}",
        'classroom_student_attach': "{{ route('classrooms.student.attach', ':classroom') }}",
        'classroom_student_detach': "{{ route('classrooms.student.detach', ':classroom') }}",
    }
</script>

@vite(['resources/js/classrooms.js', 'resources/css/dt-imports.css'])
