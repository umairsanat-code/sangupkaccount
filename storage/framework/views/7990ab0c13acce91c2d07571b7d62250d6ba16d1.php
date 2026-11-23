

<?php $__env->startSection('content'); ?>

    <style>
        /* ===== Card Styles ===== */
        .card {
            border-radius: 10px;
            border: none;
            box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #eaeaea;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* ===== Table Styles ===== */
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr.issued {
            background: #e9f9e9 !important;
        }

        .table td, .table th {
            vertical-align: middle !important;
        }

        /* ===== Buttons ===== */
        .btn-primary, .btn-success {
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 500;
        }

        /* DataTable Buttons */
        .dt-buttons button {
            margin-right: 6px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-export-csv { background-color: #28a745; color: #fff; }
        .btn-export-excel { background-color: #17a2b8; color: #fff; }
        .btn-export-pdf { background-color: #ffc107; color: #212529; }
        .btn-export-print { background-color: #6c757d; color: #fff; }

        /* Modal Styles */
        .modal-header {
            border-bottom: none;
        }

        .modal-header h5 {
            font-weight: 600;
        }

        .modal-footer {
            border-top: none;
        }
    </style>

    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Daily Sale Tickets</h2>
                    <a href="<?php echo e(route('daily_report.create')); ?>" class="btn btn-primary">
                        + Add New Ticket
                    </a>
                </div>

                <div class="card-body">

                    <!-- Filter -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" id="start_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" id="end_date" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button id="filterBtn" class="btn btn-success w-100">Filter</button>
                        </div>
                    </div>

                    <!-- Tickets Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dailyTable">
                            <thead>
                            <tr>
                                <th>Dated</th>
                                <th>Time</th>
                                <th>Pax Name</th>
                                <th>PNR</th>
                                <th>System</th>
                                <th>Issued Price</th>
                                <th>Sale Price</th>
                                <th>Pending Price</th>
                                <th>Added By</th>
                                <th>Ticket Number</th>
                                <th>Type</th>
                                <th>Detail Note</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Note Details</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="fullNoteText" style="color:black;"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="ticketForm">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Add Ticket Number</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="ticketId">
                        <div class="form-group">
                            <label>Ticket Number</label>
                            <input type="number" name="ticket_number" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success w-100">Save Ticket Number</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            var table = $('#dailyTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: 'Blfrtip',
                ajax: {
                    url: "<?php echo e(route('daily_report.index')); ?>",
                    data: function(d){
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    {data:'date', name:'date'},
                    {data:'time_limit', name:'time_limit'},
                    {data:'pax_name', name:'pax_name'},
                    {data:'pnr', name:'pnr'},
                    {data:'system', name:'system'},
                    {data:'issued_price', name:'issued_price'},
                    {data:'sale_price', name:'sale_price'},
                    {data:'pendings', name:'pendings'},
                    {data:'added_by', name:'added_by'},
                    {data:'ticket_number', name:'ticket_number'},
                    {data:'type', name:'type'},
                    {data:'remarks', name:'remarks', width:'250px'},
                    {data:'action', name:'action', orderable:false, searchable:false}
                ],
                rowCallback: function(row, data){
                    if(data.ticket_number && data.ticket_number != 0){
                        $(row).addClass('issued');
                    }
                },
                buttons: [
                    {extend:'csvHtml5', text:'<i class="fas fa-file-csv"></i> CSV', className:'btn-export-csv', title:'Daily Tickets'},
                    {extend:'excelHtml5', text:'<i class="fas fa-file-excel"></i> Excel', className:'btn-export-excel', title:'Daily Tickets'},
                    {extend:'pdfHtml5', text:'<i class="fas fa-file-pdf"></i> PDF', className:'btn-export-pdf', title:'Daily Tickets'},
                    {extend:'print', text:'<i class="fas fa-print"></i> Print', className:'btn-export-print', title:'Daily Tickets'}
                ]
            });

            // Date Filter
            $('#filterBtn').click(function(){
                table.ajax.reload();
            });

            // View Note
            $(document).on('click', '.view-note', function(){
                let fullRemarks = $(this).data('remarks');
                $('#fullNoteText').text(fullRemarks);
                $('#noteModal').modal('show');
            });

            // Add Ticket
            $(document).on('click', '.add-ticket', function(){
                $('#ticketId').val($(this).data('id'));
                $('#ticketModal').modal('show');
            });

            // Submit Ticket Form
            $('#ticketForm').submit(function(e){
                e.preventDefault();
                var id = $('#ticketId').val();
                var ticket_number = $(this).find('input[name="ticket_number"]').val();

                $.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

                $.ajax({
                    url:"<?php echo e(route('daily_report.addTicket')); ?>",
                    method:"POST",
                    data:{id:id, ticket_number:ticket_number},
                    success:function(res){
                        $('#ticketModal').modal('hide');
                        $('#ticketForm')[0].reset();
                        table.ajax.reload();
                        Swal.fire({icon:'success', title:'Ticket Added', timer:1500, showConfirmButton:false});
                    },
                    error:function(err){
                        Swal.fire({icon:'error', title:'Error', text:'Something went wrong!'});
                    }
                });
            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\testing_project\resources\views/admin/daily_report/index.blade.php ENDPATH**/ ?>