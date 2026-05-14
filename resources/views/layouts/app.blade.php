<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<meta name="csrf-token" content="{{ @csrf_token() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }} | @yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        {{-- css in build folder --}}
        @php
            $cssFiles = glob(public_path('build/assets/*.css'));
        @endphp

        @foreach ($cssFiles as $file)
            <link rel="stylesheet" href="{{ asset('build/assets/' . basename($file)) }}">
        @endforeach

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @endif
</head>

<body>
    {{-- page loader --}}
    <div id="page-loader"
        class="d-flex flex-column justify-content-center align-items-center position-fixed w-100 h-100"
        style="z-index:9999;width: 100vw; height: 100vh; backdrop-filter: blur(5px);">
        <p class="fs-1 fw-bold">
            <i class="bi bi-mortarboard-fill text-black bg-warning py-1 px-2 rounded"></i>
        </p>
        <div class="progress w-25" role="progressbar" aria-label="Example 1px high" aria-valuenow="25" aria-valuemin="0"
            aria-valuemax="100" style="height: 2px">
            <div class="progress-bar bg-warning" style="width: 25%"></div>
        </div>
    </div>


    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        {{-- Vite JS --}}
    @else
        {{-- js in build folder --}}
        @php
            $jsFiles = glob(public_path('build/assets/*.js'));
        @endphp

        @foreach ($jsFiles as $file)
            <script src="{{ asset('build/assets/' . basename($file)) }}"></script>
        @endforeach

        <!-- Bootstrap JS (with Popper) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endif
    {{-- page loader --}}
    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            if (loader) {
                loader.querySelector('div.progress-bar').style.width = '100%';
                setTimeout(() => {
                    loader.classList.add('d-none')
                }, 500);
            };
        });
    </script>
</body>

</html>
