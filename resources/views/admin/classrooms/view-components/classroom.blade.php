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
<div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
    <div class="col-12">
        <p class="border-bottom fs-4">Default Session Info</p>
    </div>
    {{-- form fields --}}
    <div class="col-12 mb-3">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Classroom Payment Method" name="classroom__payment_method" required="true"
                    placeholder="Classroom Payment Method" type="select" :selectList="['once' => 'Once', 'monthly' => 'Monthly']" />
            </div>
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Classroom Price" name="classroom__price" required="true"
                    placeholder="Classroom Price (LKR)" type="number" />
            </div>
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Classroom Start Date" name="classroom__start_date"
                    placeholder="Classroom Start Date" type="date" required="true" />
            </div>
            <div class="col-md-6 col-sm-12">
                <x-partials.form-input label="Classroom End Date" name="classroom__end_date"
                    placeholder="Classroom End Date" type="date" required="true" />
            </div>
        </div>
    </div>
</div>
