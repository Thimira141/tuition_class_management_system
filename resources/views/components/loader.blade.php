{{-- html code --}}
<div id="page-loader" class="d-flex flex-column justify-content-center align-items-center position-fixed w-100 h-100"
    style="z-index:9999;width: 100vw; height: 100vh; backdrop-filter: blur(5px);">
    <p class="fs-1 fw-bold">
        <i class="bi bi-mortarboard-fill text-black bg-warning py-1 px-2 rounded"></i>
    </p>
    <div class="progress w-25" role="progressbar" aria-label="Example 1px high" aria-valuenow="25" aria-valuemin="0"
        aria-valuemax="100" style="height: 2px">
        <div class="progress-bar bg-warning" style="width: 25%"></div>
    </div>
</div>
{{-- js script --}}
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
