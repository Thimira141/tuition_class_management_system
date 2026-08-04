<div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
    <div class="col-12">
        <p class="border-bottom fs-4">Classroom Information</p>
    </div>
    {{-- form fields --}}
    <div class="col-12 mb-3">
        <x-partials.form-input label="Classroom Name" name="classroom__name" placeholder="Classroom Name" required="true"
            type="text" classList="pe-none" />
        <x-partials.form-input label="Classroom Grade" name="classroom__grade" placeholder="Classroom Grade"
            required="true" type="text" classList="pe-none" />
        <x-partials.form-input label="Classroom Remarks" name="classroom__remarks" placeholder="Classroom Remarks"
            type="text" classList="pe-none" />
    </div>
</div>
