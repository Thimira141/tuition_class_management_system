{{-- confirm toast --}}
<div id="confirm-toast-backdrop" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center" style="z-index:100000;">
    <div id="confirm-toast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="z-index:100000;">
        <div class="toast-body">
            <span id="confirm-toast-message">Are you sure?</span>
            <div class="mt-2 pt-2 border-top">
                <button id="confirm-toast-confirm" type="button" class="btn btn-primary btn-sm me-2">Confirm</button>
                <button id="confirm-toast-close" type="button" class="btn btn-secondary btn-sm">Close</button>
            </div>
        </div>
    </div>
</div>
