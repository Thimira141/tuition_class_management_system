<nav class="navbar navbar-expand-sm bg-body-tertiary shadow px-3 position-sticky top-0 z-1"
    style="backdrop-filter: blur(5px);">
    <div class="container">
        {{-- logo --}}
        <a class="navbar-brand" href="{{ route('admin-dashboard') }}">
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
            <ul class="navbar-nav nav-pills me-auto mt-2 mt-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link btn border {{ request()->routeIs('admin-dashboard') ? 'active' : null }}"
                        href="{{ route('admin-dashboard') }}">Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border {{ request()->routeIs('admin-classrooms') ? 'active' : null }}"
                        href="{{ route('admin-classrooms') }}">
                        <i class="bi bi-person-video3 me-1"></i>Classrooms
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border {{ request()->routeIs('admin-sessions') ? 'active' : null }}"
                        href="{{ route('admin-sessions') }}">
                        <i class="bi bi-file-bar-graph me-1"></i>Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border {{ request()->routeIs('admin-students') ? 'active' : null }}"
                        href="{{ route('admin-students') }}">
                        <i class="bi bi-people-fill me-1"></i>Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border {{ request()->routeIs('admin-guardians') ? 'active' : null }}"
                        href="{{ route('admin-guardians') }}">
                        <i class="bi bi-people-fill me-1"></i>Guardians
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border" href="#announcements">
                        <i class="bi bi-file-richtext-fill me-1"></i>Exams
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border" href="#announcements">
                        <i class="bi bi-cash-coin me-1"></i>Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border" href="#announcements">
                        <i class="bi bi-calendar2-week me-1"></i>Events
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn border" href="#announcements">
                        <i class="bi bi-gear-fill me-1"></i>Settings
                    </a>
                </li>
            </ul>
            {{-- nav-menu right --}}
            <div class="d-flex my-2 my-lg-0">

                <div class="dropdown w-100">
                    <button class="btn btn-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ Auth::user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark w-100">
                        <li><a class="dropdown-item active" href="#">Action 1</a></li>
                        <li><a class="dropdown-item" href="#">Action 2</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <button class="dropdown-item text-danger-emphasis" id="log-out-form-button">
                                <i class="bi bi-box-arrow-right me-1"></i>Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
