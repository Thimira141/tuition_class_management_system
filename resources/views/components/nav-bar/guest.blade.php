<nav class="navbar navbar-expand-sm shadow px-3 position-sticky top-0 z-1" style="backdrop-filter: blur(5px);">
    {{-- logo --}}
    <a class="navbar-brand" href="{{ route('web-home') }}">
        <i class="bi bi-mortarboard-fill text-black bg-warning py-1 px-2 rounded"></i>
    </a>
    {{-- collapse button --}}
    <button class="navbar-toggler d-lg-none border-0" type="button" data-bs-toggle="collapse"
        data-bs-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false"
        aria-label="Toggle navigation">
        <i class="bi bi-list"></i>
    </button>
    {{-- nav-menu --}}
    <div class="collapse navbar-collapse" id="collapsibleNavId">
        {{-- nav-menu left --}}
        <ul class="navbar-nav me-auto mt-2 mt-lg-0">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('web-home') }}" aria-current="page">Home <span
                        class="visually-hidden">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('web-home') }}#announcements">Announcements</a>
            </li>
        </ul>
        {{-- nav-menu right --}}
        <div class="d-flex my-2 my-lg-0">
            <a class="btn btn-secondary my-2 my-sm-0" href="{{ route('login-page') }}">
                Login
            </a>
        </div>
    </div>
</nav>
