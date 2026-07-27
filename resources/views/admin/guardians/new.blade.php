<!-- Modal trigger button -->
{{-- <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#new-guardian-model">
    Add new Guardian
</button> --}}

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="new-guardian-model" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="NFM-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <form action="{{ $formAction??null }}" method="post" enctype="multipart/form-data" class="ajax-form" id="new-edit-guardian-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="NFM-title">
                        Guardian Form
                    </h5>
                    <button type="reset" class="btn-close" aria-label="Close"
                     @if ($backTo)
                        data-bs-target="{{ $backTo }}" data-bs-toggle="modal"
                    @else
                        data-bs-dismiss="modal"
                    @endif></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
                            <div class="col-12">
                                <p class="border-bottom fs-4">Guradian Information</p>
                            </div>
                            {{-- image upload --}}
                            <div class="col-md-2 col-sm-12 mb-3 d-flex align-items-center">
                                <div
                                    class="w-100 h-auto position-relative cover-image-action-btn-hover-cover p-md-2 p-sm-5 rounded-circle bg-body-secondary shadow">
                                    <img src="{{ asset('images/placeholder-image-member.svg') }}" class="img-fluid rounded-top w-100" alt=""
                                        id="guardian__cover_img-preview"
                                        onerror="this.src='{{ asset('images/placeholder-image-member.svg') }}'" />
                                    <label
                                        class="btn btn-secondary position-absolute bottom-0 end-0 rounded-pill cover-image-action-btn opacity-0"
                                        title="Edit Cover Image" for="guardian__cover_img">
                                        <i class="bi bi-pencil-square"></i>
                                    </label>
                                    <input type="file" name="guardian__cover_img" id="guardian__cover_img"
                                        class="visually-hidden" data-target-img="#guardian__cover_img-preview"
                                        accept="image/*" onchange="utility.previewSelectedImage(this);">
                                </div>
                            </div>
                            {{-- form fields --}}
                            <div class="col-md-9 col-sm-12 mb-3">
                                <x-partials.form-input label="Name" name="guardian__name" placeholder="Guardian Name" required="true" type="text" />
                                <x-partials.form-input label="Address" name="guardian__address" placeholder="Guardian Address" required="true" type="text" />
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <x-partials.form-input label="Mobile" name="guardian__tel" placeholder="Guardian Mobile" required="true" type="text" />
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <x-partials.form-input label="Email" name="guardian__email" placeholder="Guardian Email" type="text" />
                                    </div>
                                    <x-partials.form-input label="NIC" name="guardian__nic" placeholder="Guardian NIC" required="true" type="text" />
                                    <x-partials.form-input label="Remarks" name="guardian__remarks" placeholder="Guardian Remarks" type="text" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary"
                    @if ($backTo)
                        data-bs-target="{{ $backTo }}" data-bs-toggle="modal"
                    @else
                        data-bs-dismiss="modal"
                    @endif
                    >
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

