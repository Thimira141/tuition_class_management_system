{{-- new student model --}}
<!-- Modal trigger button -->
{{-- <button
    type="button"
    class="btn btn-primary btn-lg"
    data-bs-toggle="modal"
    data-bs-target="#new-student-model"
>
    New Student Launch
</button> --}}

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="new-student-model" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="NSM-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <form action="" method="post" class="ajax-form" id="new-edit-student-form" enctype="multipart/form-data" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="NSM-title">
                        Student Form
                    </h5>
                    <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
                            <div class="col-12">
                                <p class="border-bottom fs-4">Student Information</p>
                            </div>
                            {{-- image upload --}}
                            <div class="col-md-2 col-sm-12 mb-3 d-flex align-items-center">
                                <div
                                    class="w-100 h-auto position-relative cover-image-action-btn-hover-cover p-md-2 p-sm-5 rounded-circle bg-body-secondary shadow">
                                    <img src="{{ asset('images/placeholder-image-member.svg') }}" class="img-fluid rounded-top w-100" alt="cover img"
                                        id="student__cover_img-preview" data-image-reset="{{ asset('images/placeholder-image-member.svg') }}"
                                        onerror="this.src='{{ asset('images/placeholder-image-member.svg') }}'" />
                                    <label
                                        class="btn btn-secondary position-absolute bottom-0 end-0 rounded-pill cover-image-action-btn opacity-0"
                                        title="Edit Cover Image" for="student__cover_img">
                                        <i class="bi bi-pencil-square"></i>
                                    </label>
                                    <input type="file" name="student__cover_img" id="student__cover_img"
                                        class="visually-hidden" data-target-img="#student__cover_img-preview"
                                        accept="image/*" onchange="utility.previewSelectedImage(this);">
                                </div>
                            </div>
                            {{-- form fields --}}
                            <div class="col-md-9 col-sm-12 mb-3">
                                <x-partials.form-input label="Student Name" name="student__name" placeholder="Student Name" required="true" type="text" />
                                <x-partials.form-input label="Student Address" name="student__address" placeholder="Student Address" required="true" type="text" />
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <x-partials.form-input label="Student Mobile" name="student__tel" placeholder="Student Mobile" required="true" type="text" />
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <x-partials.form-input label="Student Email" name="student__email" placeholder="Student Email" type="email" required="true" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <x-partials.form-input label="Student Date of Birth" name="student__dob" placeholder="Student Date of Birth" required="true" type="date" />
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <x-partials.form-input label="Student Joined Date" name="student__joined_at" placeholder="Student Joined Date" required="true" type="date" />
                                    </div>
                                </div>
                                <x-partials.form-input label="Student NIC Number" name="student__nic" placeholder="Student NIC Number" type="text" />
                                <x-partials.form-input label="Student Remarks" name="student__remarks" placeholder="Student Remarks" type="text" />
                            </div>
                        </div>
                        <div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
                            <div class="col-12">
                                <p class="border-bottom fs-4">Guardian Information</p>
                            </div>
                            <div class="col-11">
                                <div class="row">
                                    <div class="col-md-10 col-sm-12">
                                        <x-partials.form-input label="Guardian" name="student__guardian_id" placeholder="Select Guardian" required="true" type="select" classList="form-select" />
                                    </div>
                                    <div class="col-md-2 col-sm-12 d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary btn-lg" id="new-guardian-add-model-init-btn" data-bs-toggle="modal" data-bs-target="#new-guardian-model">
                                            Add New Guardian
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('admin.guardians.new', ['backTo' => '#new-student-model', 'formAction' => route('guardians.ajax.store')])
