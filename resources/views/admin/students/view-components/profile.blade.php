<div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
    <div class="col-12">
        <p class="border-bottom fs-4">Student Information</p>
    </div>
    {{-- image upload --}}
    <div class="col-md-2 col-sm-12 mb-3 d-flex align-items-center">
        <div
            class="w-100 h-auto position-relative cover-image-action-btn-hover-cover p-md-2 p-sm-5 rounded-circle bg-body-secondary shadow">
            <img src="..." class="img-fluid rounded-top w-100" alt="" id="student__cover_img-preview"
                onerror="this.src='{{ asset('images/placeholder-image-member.svg') }}'" />
        </div>
    </div>
    {{-- form fields --}}
    <div class="col-md-9 col-sm-12 mb-3">
        <x-partials.form-input label="Student Name" name="student__name" placeholder="Student Name" required="true"
            type="text" classList="pe-none," />
        <x-partials.form-input label="Student Address" name="student__address" placeholder="Student Address"
            required="true" type="text" classList="pe-none," />
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Student Mobile" name="student__tel" placeholder="Student Mobile"
                    required="true" type="text" classList="pe-none," />
            </div>
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Student Email" name="student__email" placeholder="Student Email"
                    type="email" required="true" classList="pe-none," />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Student Date of Birth" name="student__dob"
                    placeholder="Student Date of Birth" required="true" type="date" classList="pe-none," />
            </div>
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Student Joined Date" name="student__joined_at"
                    placeholder="Student Joined Date" required="true" type="date" classList="pe-none," />
            </div>
        </div>
        <x-partials.form-input label="Student NIC Number" name="student__nic" placeholder="Student NIC Number"
            type="text" classList="pe-none," />
        <x-partials.form-input label="Student Remarks" name="student__remarks" placeholder="Student Remarks"
            type="text" classList="pe-none," />
    </div>
</div>
<div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
    <div class="col-12">
        <p class="border-bottom fs-4">Guardian Information</p>
    </div>
    <div class="col-11">
        <div class="row">
            <x-partials.form-input label="Guardian Name" name="guardian__name" placeholder="Guardian Name"
                required="true" type="text" classList="pe-none," />
            <x-partials.form-input label="Guardian NIC" name="guardian__nic" placeholder="Guardian NIC"
                required="true" type="text" classList="pe-none," />
            <x-partials.form-input label="Guardian Mobile" name="guardian__tel" placeholder="Guardian Mobile"
                required="true" type="text" classList="pe-none," />
            <x-partials.form-input label="Guardian Remarks" name="guardian__remarks" placeholder="Guardian Remarks"
                type="text" classList="pe-none," />
        </div>
    </div>
</div>
