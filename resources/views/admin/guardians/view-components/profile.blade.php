<div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
    <div class="col-12">
        <p class="border-bottom fs-4">Guradian Information</p>
    </div>
    {{-- image upload --}}
    <div class="col-md-2 col-sm-12 mb-3 d-flex align-items-center">
        <div
            class="w-100 h-auto position-relative cover-image-action-btn-hover-cover p-md-2 p-sm-5 rounded-circle bg-body-secondary shadow">
            <img src="{{ asset('images/placeholder-image-member.svg') }}" class="img-fluid rounded-top w-100"
                alt="" id="guardian__cover_img-preview"
                onerror="this.src='{{ asset('images/placeholder-image-member.svg') }}'" />
        </div>
    </div>
    {{-- form fields --}}
    <div class="col-md-9 col-sm-12 mb-3">
        <x-partials.form-input label="Name" name="guardian__name" placeholder="Guardian Name" required="true"
            type="text" classList="pe-none" />
        <x-partials.form-input label="Address" name="guardian__address" placeholder="Guardian Address" required="true"
            type="text" classList="pe-none" />
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Mobile" name="guardian__tel" placeholder="Guardian Mobile" required="true"
                    type="text" classList="pe-none" />
            </div>
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Email" name="guardian__email" placeholder="Guardian Email"
                    type="text" classList="pe-none" />
            </div>
            <x-partials.form-input label="NIC" name="guardian__nic" placeholder="Guardian NIC" required="true"
                type="text" classList="pe-none" />
            <x-partials.form-input label="Remarks" name="guardian__remarks" placeholder="Guardian Remarks"
                type="text" classList="pe-none" />
        </div>
    </div>
</div>
