<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.css">

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container">
            <h2>Messages List</h2>
            <div class="d-flex justify-content-end mb-3">

                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createExpenseModal">New Message</button>

            </div>
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>


            <div class="card">
                <div class="card-body">
                    
                    <table id="messageTable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sender Name</th>
                            <th>Receive Name</th>
                            <!--<th>Ticket Number</th>-->
                            <th>Message</th>
                            <th>Is_Read</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="messageForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="createExpenseModalLabel">Add New Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                       
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" name="message" rows="4" placeholder="Enter your message" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>






<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.ticket-select').select2({
                placeholder: "Search Ticket",
                allowClear: true,
                width: '100%',
                color:black,
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
            let ticketId = "1";
            $(document).ready(function () {
                var table = $('#messageTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "<?php echo e(route('import_messsage')); ?>",
                        // data: function (d) {
                        //     d.startDate = $('#startDate').val();
                        //     d.endDate = $('#endDate').val();
                        // }
                    },
                    columns: [
                        { data: 'date', name: 'date' },
                        { data: 'sender_name', name: 'sender_name' },
                        { data: 'receive_name', name: 'receive_name' },
                        { data: 'message', name: 'message' },
                        // { data: 'ticket_number', name: 'ticket_number' },
                        { data: 'is_read', name: 'is_read' },
                        { data: 'actions', name: 'actions' },

                    ],
                    drawCallback: function (settings) {
                        // var totalAmount = settings.json.totalAmount || '0.00';
                        // var totalCount = settings.json.totalCount || '0';

                        // $('#totalAmount').text(totalAmount);
                        // $('#totalCount').text(totalCount);
                    }
                });

                $('#filterBtn').on('click', function () {
                    table.ajax.reload();
                });
            });
             $('#messageTable').on('click', '.delete', function () {
                const paymentId = $(this).data('id'); // Get the payment ID

                // SweetAlert Confirmation Dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with the AJAX request if confirmed
                        $.ajax({
                            url: `/messages/${paymentId}`, // The DELETE route
                            method: 'POST',              // Use DELETE method
                            headers: {                   // Include CSRF token for security
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                // Show SweetAlert success message
                                Swal.fire(
                                    'Deleted!',
                                    'The messages has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // Reload the page
                                });
                            },
                            error: function (xhr) {
                                // Show SweetAlert error message
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    Swal.fire('Error', xhr.responseJSON.message, 'error');
                                } else {
                                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $("#messageForm").submit(function(e) {
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    url: "<?php echo e(route('messages')); ?>", // Ensure this route exists in web.php
                    type: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>" // CSRF token for security
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: "success",
                            title: "Message Sent",
                            text: "Your message has been successfully sent!",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#createExpenseModal').modal('hide'); // Close modal after submission
                        $("#messageForm")[0].reset(); // Reset the form
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = "Something went wrong!";

                        if (errors) {
                            errorMessage = Object.values(errors).flat().join("\n");
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: errorMessage
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/import_messages/index.blade.php ENDPATH**/ ?>