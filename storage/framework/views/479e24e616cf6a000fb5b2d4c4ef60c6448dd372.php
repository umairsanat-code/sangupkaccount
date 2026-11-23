<?php $__env->startSection('content'); ?>
    <div class="content-wrapper py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="fw-bold text-primary">📘 Expense List</h2>
                <button class="btn btn-success shadow-sm" data-toggle="modal" data-target="#createExpenseModal">
                    ➕ Add New Expense
                </button>
            </div>

            
            <?php if(session('success')): ?>
                <div class="alert alert-success shadow-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8 d-flex flex-wrap gap-3">
                            <label class="fw-bold">Start Date:</label>
                            <input type="date" id="startDate" class="form-control" style="width:160px">

                            <label class="fw-bold">End Date:</label>
                            <input type="date" id="endDate" class="form-control" style="width:160px">

                            <button id="filterBtn" class="btn btn-primary px-4">🔍 Filter</button>
                        </div>

                        <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                            <span class="d-block"><strong>Total Records:</strong> <span id="totalCount" class="text-info fw-bold">0</span></span>
                            <span class="d-block"><strong>Total Amount:</strong> <span id="totalAmount" class="text-success fw-bold">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- Export Buttons -->
                    <div class="mb-2 text-end">
                        <button id="csvBtn" class="btn btn-outline-success btn-sm me-2 shadow-sm">⬇️ CSV</button>
                        <button id="pdfBtn" class="btn btn-outline-danger btn-sm shadow-sm">📄 PDF</button>
                    </div>

                    <table id="expenseTable" class="table table-bordered table-striped table-hover w-100">
                        <thead class="table-dark text-center">
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount (€)</th>
                            <th>Description</th>
                            <th class="no-export" style="width:120px;">Actions</th>
                        </tr>
                        </thead>
                    </table>

                </div>
            </div>

        </div>
    </div>


    <!-- ============================
     CREATE EXPENSE MODAL
============================= -->
    <div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">

                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">➕ Add New Expense</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="<?php echo e(route('expenses.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="modal-body p-4">

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" name="date" class="form-control rounded-3" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category</label>
                                <input type="text" name="category" class="form-control rounded-3" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control rounded-3" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" rows="3" class="form-control rounded-3"></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                            💾 Save Expense
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ============================
            EDIT EXPENSE MODAL
    ============================= -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">

                <div class="modal-header bg-warning text-dark rounded-top-4">
                    <h5 class="modal-title fw-bold">✏️ Edit Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editExpenseForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="modal-body p-4">

                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" id="edit-date" name="date" class="form-control rounded-3" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Category</label>
                                <input type="text" id="edit-category" name="category" class="form-control rounded-3" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Amount</label>
                                <input type="number" step="0.01" id="edit-amount" name="amount" class="form-control rounded-3" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea id="edit-description" name="description" rows="3" class="form-control rounded-3"></textarea>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="submit" class="btn btn-warning text-dark px-4 rounded-pill shadow-sm fw-bold">
                            ✔ Update Expense
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <!-- DataTables + Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

    <script>
        $(function () {

            var table = $('#expenseTable').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Blfrtip',
                lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],

                buttons: [
                    {
                        extend: 'csvHtml5',
                        title: 'Expense List',
                        className: 'd-none buttons-csv',
                        filename: 'Expense_List_Report',
                        exportOptions: {
                            modifier: { page: 'all' },
                            columns: ':not(.no-export)' // <-- Action column not exported
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Expense Report',
                        className: 'd-none buttons-pdf',
                        filename: 'Expense_List_Report',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            modifier: { page: 'all' },
                            columns: ':not(.no-export)' // <-- Remove Edit/Delete from PDF
                        }
                    }
                ],

                ajax: {
                    url: "<?php echo e(route('expenses.index')); ?>",
                    data: function (d) {
                        d.startDate = $('#startDate').val();
                        d.endDate = $('#endDate').val();
                    }
                },

                columns: [
                    { data: 'date', name: 'date' },
                    { data: 'category', name: 'category' },
                    { data: 'amount', name: 'amount' },
                    { data: 'description', name: 'description' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'no-export' }
                ],

                drawCallback: function (settings) {
                    $('#totalAmount').text(settings.json.totalAmount);
                    $('#totalCount').text(settings.json.totalCount);
                }
            });

            $('#filterBtn').click(() => table.ajax.reload());

            $('#csvBtn').click(() => table.button('.buttons-csv').trigger());
            $('#pdfBtn').click(() => table.button('.buttons-pdf').trigger());
        });


        // Edit Modal Script
        $('#editExpenseModal').on('show.bs.modal', function (event) {
            const btn = $(event.relatedTarget);
            $('#editExpenseForm').attr('action', `/expenses/${btn.data('id')}`);
            $('#edit-date').val(btn.data('date'));
            $('#edit-category').val(btn.data('category'));
            $('#edit-amount').val(btn.data('amount'));
            $('#edit-description').val(btn.data('description'));
        });
    </script>

    <style>
        .table-hover tbody tr:hover {
            background-color: #eef7ff;
        }
        .btn {
            border-radius: 25px;
        }
        .dataTables_filter input {
            border-radius: 20px !important;
        }
    </style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\testing_project\resources\views/admin/expenses/index.blade.php ENDPATH**/ ?>