{{-- view student info model --}}
<!-- Modal trigger button -->
<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#view-student-model">
    View student Launch
</button>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="view-student-model" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="VSM-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="VSM-title">
                    View Student | STU-1245
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 model-fixed-height">
                {{-- tab-list --}}
                <nav class="position-sticky top-0 bg-body-tertiary rounded shadow z-1"
                    style="backdrop-filter: blur(5px);">
                    <ul class="nav nav-pills nav-fill mb-3 py-1" id="pills-tab" role="tablist">
                        <li class="nav-item m-2" role="presentation">
                            <button class="nav-link border active" id="pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#profile-tab-pane" type="button" role="tab"
                                aria-controls="profile-tab-pane" aria-selected="true">Profile</button>
                        </li>
                        <li class="nav-item m-2" role="presentation">
                            <button class="nav-link border" id="attendance-tab-pane-tab" data-bs-toggle="pill"
                                data-bs-target="#attendance-tab-pane" type="button" role="tab"
                                aria-controls="attendance-tab-pane" aria-selected="false">Attendance</button>
                        </li>
                        <li class="nav-item m-2" role="presentation">
                            <button class="nav-link border" id="payments-tab-pane-tab" data-bs-toggle="pill"
                                data-bs-target="#payments-tab-pane" type="button" role="tab"
                                aria-controls="payments-tab-pane" aria-selected="false">Payments</button>
                        </li>
                        <li class="nav-item m-2" role="presentation">
                            <button class="nav-link border" id="exams-tab-pane-tab" data-bs-toggle="pill"
                                data-bs-target="#exams-tab-pane" type="button" role="tab"
                                aria-controls="exams-tab-pane" aria-selected="false">Exams</button>
                        </li>
                    </ul>
                </nav>
                {{-- tab pane --}}
                <div class="tab-content" id="VSM-tab-pane">
                    <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel"
                        aria-labelledby="profile-tab" tabindex="0">
                        <div class="container-fluid">
                            @include('admin.students.view-components.profile')
                        </div>
                    </div>
                    <div class="tab-pane fade" id="attendance-tab-pane" role="tabpanel" aria-labelledby="attendance-tab"
                        tabindex="0">
                        <p class="fs-1">attendance-tab-pane</p>
                    </div>
                    <div class="tab-pane fade" id="payments-tab-pane" role="tabpanel" aria-labelledby="payments-tab"
                        tabindex="0">
                        <p class="fs-1">payments-tab-pane</p>
                    </div>
                    <div class="tab-pane fade" id="exams-tab-pane" role="tabpanel" aria-labelledby="exams-tab"
                        tabindex="0">
                        <p class="fs-1">exams-tab-pane</p>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary d-none">Save</button>
            </div>
        </div>
    </div>
</div>
