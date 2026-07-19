@extends('layouts.app')

@section('content')
    {{-- nav bar --}}
    @include('components.nav-bar.guest')
    {{-- login form --}}
    <div class="container-md d-flex justify-content-center align-items-center"
     style="height: calc(100vh - 100px)">

    <div class="row bg-body-secondary shadow p-4 rounded w-75">
        <!-- Icon -->
        <div class="col-md-4 d-flex justify-content-center align-items-center mb-3 mb-md-0">
            <i class="bi bi-person-lock opacity-75" style="font-size: 10em"></i>
        </div>

        <!-- Divider (vertical on md+, horizontal on small) -->
        <div class="col-12 d-md-none border-top mb-3"></div>
        <div class="col-md-1 d-none d-md-flex border-start"></div>

        <!-- Form -->
        <div class="col-md-7">
            <form action="{{ route('login') }}" method="post" class="ajax-form">
                <p class="fs-1 text-center mb-4">User Login</p>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="email-input">@</span>
                    <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email"
                           aria-describedby="email-input" value="admin-user@sample.com">
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-password">
                        <i class="bi bi-key-fill"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Password" aria-label="Password"
                           aria-describedby="basic-password" value="password-1234567890">
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

@endsection
