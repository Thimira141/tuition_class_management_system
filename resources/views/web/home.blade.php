@extends('layouts.app')


@section('title', 'Home')

@section('content')

    {{-- nav bar --}}
    @include('components.nav-bar.guest')

    <div class="container-fluid">
        {{-- hero section --}}
        <section class="row z-0" id="hero">
            <div class="col-12 d-flex flex-column justify-content-center align-items-center p-5"
                style="min-height: calc(100vh - 20em);
                    background-image: linear-gradient(180deg, var(--bs-warning), transparent, var(--bs-warning));">

                <div class="text-center">
                    <h1 class="display-1 fw-bold">Welcome to X Tuition Classroom</h1>
                    <p class="fs-2">Your gateway to attendance, exams, and payments — all managed securely in one place.
                    </p>
                </div>

                <div class="mt-5">
                    <a class="btn btn-lg btn-primary rounded-pill m-1" href="{{ route('login-page') }}">
                        Login as Student / Parent <i class="bi bi-arrow-right-short ms-1"></i>
                    </a>
                    <a class="btn btn-lg btn-primary rounded-pill m-1" href="{{ route('login-page') }}">
                        Login as Admin <i class="bi bi-arrow-right-short ms-1"></i>
                    </a>
                </div>
            </div>
        </section>

        {{-- feature overview --}}
        <section class="row mt-5 justify-content-around z-0" id="features">
            <div class="col-md-3 col-sm-12 mb-3">
                <div class="card h-100 text-center shadow">
                    <div class="card-body">
                        <p class="fs-3"><i class="bi bi-person-arms-up me-1"></i>Students</p>
                        <hr>
                        <p class="fs-5">Check your attendance, exam results, and upcoming events</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 mb-3">
                <div class="card h-100 text-center shadow">
                    <div class="card-body">
                        <p class="fs-3"><i class="bi bi-person-workspace me-1"></i>Parents</p>
                        <hr>
                        <p class="fs-5">Track your child’s progress and manage tuition payments easily</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 mb-3">
                <div class="card h-100 text-center shadow">
                    <div class="card-body">
                        <p class="fs-3"><i class="bi bi-person-fill-gear me-1"></i>Admin/Teacher</p>
                        <hr>
                        <p class="fs-5">Simplify class records and focus on teaching.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- announcements --}}
        <section class="row mt-5 justify-content-around z-0" id="announcements">
            <div class="col-11 shadow">
                <p class="fs-2"><i class="bi bi-bell me-1"></i> Announcements</p>
                <hr>
                <div class="row">
                    // events info
                </div>
            </div>
        </section>
    </div>

    {{-- footer --}}
    <nav class="nav justify-content-center text-center p-1 mt-5 shadow">
        <p class="text-primary">For inquiries, reach us at [phone/email]</p>
        <i class="bi bi-dot mx-2 d-none d-md-block"></i>
        <small class="text-secondary">© 2026 X Tuition Classroom Management System</small>
    </nav>




@endsection
