<!-- Modal trigger button -->
{{-- <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#new-classroom-model">
    Add new classroom
</button> --}}

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="new-classroom-model" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="NFM-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <form action="#" method="post" enctype="multipart/form-data" class="ajax-form"
            id="new-edit-classroom-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="NFM-title">
                        Classroom Form
                    </h5>
                    <button type="reset" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row py-2 mb-3 bg-body-tertiary shadow rounded justify-content-evenly">
                            <div class="col-12">
                                <p class="border-bottom fs-4">Classroom Information</p>
                            </div>
                            {{-- form fields --}}
                            <div class="col-12 mb-3">
                                <x-partials.form-input label="Classroom Name" name="classroom__name"
                                    placeholder="Classroom Name" required="true" type="text" />
                                <x-partials.form-input label="Classroom Grade" name="classroom__grade"
                                    placeholder="Classroom Grade" required="true" type="text" />
                                <x-partials.form-input label="Classroom Remarks" name="classroom__remarks"
                                    placeholder="Classroom Remarks" type="text" />
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
                                        <x-partials.form-input label="Classroom Payment Method"
                                            name="classroom__payment_method" required="true"
                                            placeholder="Classroom Payment Method" type="select" :selectList="['once' => 'Once', 'monthly' => 'Monthly']" />
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <x-partials.form-input label="Classroom Price" name="classroom__price"
                                            required="true" placeholder="Classroom Price (LKR)" type="number" />
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-secondary" aria-label="Close"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
