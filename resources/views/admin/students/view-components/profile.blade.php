<div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
    <div class="col-12">
        <p class="border-bottom fs-4">Student Information</p>
    </div>
    {{-- image upload --}}
    <div class="col-md-2 col-sm-12 mb-3 d-flex align-items-center">
        <div
            class="w-100 h-auto position-relative cover-image-action-btn-hover-cover p-md-2 p-sm-5 rounded-circle bg-body-secondary shadow">
            <img src="..." class="img-fluid rounded-top w-100" alt="" id="student_cover_img-preview"
                onerror="this.src='{{ asset('images/placeholder-image-member.svg') }}'" />
        </div>
    </div>
    {{-- form fields --}}
    <div class="col-md-9 col-sm-12 mb-3">
        <x-partials.form-input label="Name" name="name" placeholder="Student Name" required="true" type="text"
            classList="pe-none," />
        <x-partials.form-input label="Address" name="address" placeholder="Student Address" required="true"
            type="text" classList="pe-none," />
        <x-partials.form-input label="Mobile" name="mobile" placeholder="Student Mobile" required="true" type="text"
            classList="pe-none," />
        <x-partials.form-input label="Email" name="email" placeholder="Student Email" required="true" type="email"
            classList="pe-none," />
    </div>
</div>
<div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
    <div class="col-12">
        <p class="border-bottom fs-4">Guardian Information</p>
    </div>
    <div class="col-11">
        <x-partials.form-input label="Name" name="name" placeholder="Guardian Name" required="true" type="text"
            classList="pe-none," />
        <x-partials.form-input label="Address" name="address" placeholder="Guardian Address" required="true"
            type="text" classList="pe-none," />
        <x-partials.form-input label="Mobile" name="mobile" placeholder="Guardian Mobile" required="true"
            type="text" classList="pe-none," />
    </div>
</div>
