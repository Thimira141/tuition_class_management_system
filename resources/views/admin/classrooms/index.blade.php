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
                            <th scope="col">Grade</th>
                            <th scope="col">Students</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>

    @include('admin.classrooms.new')
    @include('admin.classrooms.view')
@endsection
