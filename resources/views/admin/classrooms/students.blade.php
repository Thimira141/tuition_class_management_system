{{-- modal handle students index/attach/detach for classroom --}}

<!-- Modal trigger button -->
<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#new-classroom-students-model">
    Add new classroom students
</button>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="new-classroom-students-model" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="CS-FM-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="CS-FM-title">
                    Add/Remove Students from class
                </h5>
                {{-- todo make open the previous modal->student modal --}}
                <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="dt-classroom-students-index-table" class="table table-hover m-0 rounded">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">Name</th>
                                        <th scope="col">DOB</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" aria-label="Close"
                    data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary d-none">Save</button>
            </div>
        </div>
    </div>
</div>
