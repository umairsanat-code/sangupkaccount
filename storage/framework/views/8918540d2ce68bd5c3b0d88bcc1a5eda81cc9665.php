<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.css">

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper p-3">
        <div class="container">

            <!-- Top Bar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold">✈ Airline Management</h3>

                <div>


                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAirlineModal">
                        + Create Airline
                    </button>
                </div>
            </div>

            <!-- DataTable -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Airline List</h5>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped" id="airline-table">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Country</th>
                            <th width="150">Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>


    <!-- ========================= -->
    <!-- ADD AIRLINE MODAL -->
    <!-- ========================= -->
    <div class="modal fade" id="addAirlineModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add New Airline</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addAirlineForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control">
                                <small class="text-danger error-name"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Code</label>
                                <input type="text" name="code" class="form-control">
                                <small class="text-danger error-code"></small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control">
                                <small class="text-danger error-country"></small>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Save Airline</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- EDIT AIRLINE MODAL -->
    <!-- ========================= -->
    <div class="modal fade" id="editAirlineModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Airline</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editAirlineForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <input type="hidden" id="airline_id">

                    <div class="modal-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label>Name</label>
                                <input type="text" id="edit_name" name="name" class="form-control">
                                <small class="text-danger error-name"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Code</label>
                                <input type="text" id="edit_code" name="code" class="form-control">
                                <small class="text-danger error-code"></small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Country</label>
                                <input type="text" id="edit_country" name="country" class="form-control">
                                <small class="text-danger error-country"></small>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-warning">Update Airline</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.js"></script>

    <!-- DATATABLE + EXPORT PLUGINS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(document).ready(function () {

            /* ----------------------------
               Initialize DataTable
            -----------------------------*/
            var table = $('#airline-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('airlines.index')); ?>",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'country', name: 'country' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                dom: 'Bfltip',
                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: 'Export CSV',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        className: 'btn btn-danger btn-sm',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        customize: function(doc) {
                            // Add Company info at the top
                            doc.content.splice(0, 0, {
                                text: [
                                    { text: 'Sangu Travel & Tours\n', bold: true, fontSize: 16 },
                                    { text: "Av. d'Alfons XIII, 496, 08913 Santa Coloma de Gramenet, Barcelona, Spain\n", fontSize: 12 },
                                    { text: 'Email: Sangutravel@gmail.com | Web: www.sangugroup.com\n', fontSize: 12 },
                                    { text: 'Phone: +34 631 02 88 71\n\n', fontSize: 12 }
                                ],
                                alignment: 'center'
                            });

                            // Center table
                            doc.styles.tableHeader.alignment = 'center';
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';

                            // Optional: Adjust table width
                            doc.content[doc.content.length - 1].table.widths = ['auto', '*', '*', '*', '*'];
                        }
                    }
                ],
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
                language: {
                    paginate: {
                        previous: "<i class='bi bi-arrow-left'></i> Previous",
                        next: "Next <i class='bi bi-arrow-right'></i>"
                    }
                }
            });

            /* ----------------------------
               Export Buttons
            -----------------------------*/
            $("#csvExport").click(function() {
                table.button('.buttons-csv').trigger();
            });

            $("#pdfExport").click(function() {
                table.button('.buttons-pdf').trigger();
            });


            /* =============================
               Add Airline
            ==============================*/
            $('#addAirlineForm').submit(function(e){
                e.preventDefault();

                $.ajax({
                    url: "<?php echo e(route('airlines.store')); ?>",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#addAirlineModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success!', response.success, 'success');
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            $('.error-' + key).text(errors[key][0]);
                        }
                    }
                });
            });


            /* =============================
               Edit Button Click
            ==============================*/
            $(document).on('click', '.edit', function () {
                var id = $(this).data('id');

                $.get("<?php echo e(url('airlines')); ?>/" + id + "/edit", function (data) {
                    $('#editAirlineModal').modal('show');
                    $('#airline_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_code').val(data.code);
                    $('#edit_country').val(data.country);
                });
            });


            /* =============================
               Update Airline
            ==============================*/
            $('#editAirlineForm').submit(function(e){
                e.preventDefault();

                var id = $('#airline_id').val();

                $.ajax({
                    url: "<?php echo e(url('airlines')); ?>/" + id,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#editAirlineModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Updated!', response.success, 'success');
                    },
                    error: function(xhr){
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            $('.error-' + key).text(errors[key][0]);
                        }
                    }
                });
            });


            /* =============================
               Delete Airline
            ==============================*/
            $(document).on('click', '.delete', function(){
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Record will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true
                }).then((res)=>{
                    if(res.isConfirmed){
                        $.ajax({
                            url: "<?php echo e(url('airlines')); ?>/" + id,
                            method: "DELETE",
                            data: {_token: '<?php echo e(csrf_token()); ?>'},
                            success:function(response){
                                Swal.fire("Deleted!", response.success, "success");
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\testing_project\resources\views/admin/airline/index.blade.php ENDPATH**/ ?>