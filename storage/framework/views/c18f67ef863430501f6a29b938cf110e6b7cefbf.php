<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.css">

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
<br>
        <div class="container mt-4">






            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAgentModal">
                    Create Agent
                </button>
            </div>

            <!-- Card for Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Agent List</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="agent-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Agent Modal -->
    <div class="modal fade" id="addAgentModal" tabindex="-1" aria-labelledby="addAgentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addAgentForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAgentLabel">Add Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <small class="text-danger error-name"></small>
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number" class="form-control">
                            <small class="text-danger error-contact_number"></small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" class="form-control">
                            <small class="text-danger error-email"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Agent Modal -->
    <div class="modal fade" id="editAgentModal" tabindex="-1" aria-labelledby="editAgentLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editAgentForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAgentLabel">Edit Agent</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="agent_id">
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control">
                            <small class="text-danger error-name"></small>
                        </div>
                        <div class="form-group">
                            <label for="edit_contact_number">Contact Number</label>
                            <input type="text" name="contact_number" id="edit_contact_number" class="form-control">
                            <small class="text-danger error-contact_number"></small>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email</label>
                            <input type="text" name="email" id="edit_email" class="form-control">
                            <small class="text-danger error-email"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- Modal to View Tickets -->
    <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">View Tickets</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form to filter tickets -->
                    <form method="GET" id="viewTicketsForm">
                        <input type="hidden" id="form_agent_id" name="agent_id">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="form_start_date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="form_end_date" name="end_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">View Tickets</button>
                    </form>

                    <!-- Container for tickets table -->
                    <div id="ticketsTable" style="margin-top:20px;"></div>
                </div>
            </div>
        </div>
    </div>




<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.js"></script>

    <script>
        $(function() {
            var table = $('#agent-table').DataTable({
                processing: true,
                ajax: "<?php echo e(route('agents.index')); ?>",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'contact_number', name: 'contact_number' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Add Agent
            $('#addAgentForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "<?php echo e(route('agents.store')); ?>",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addAgentModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', response.success, 'success');
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            $('.error-' + key).text(errors[key][0]);
                        }
                    }
                });
            });

            // Edit Agent
            $(document).on('click', '.edit', function() {
                var id = $(this).data('id');
                $.get("<?php echo e(url('agents')); ?>/" + id + "/edit", function(data) {
                    $('#editAgentModal').modal('show');
                    $('#agent_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_contact_number').val(data.contact_number);
                    $('#edit_email').val(data.email);
                });
            });

            // Update Agent
            $('#editAgentForm').submit(function(e) {
                e.preventDefault();
                var id = $('#agent_id').val();
                $.ajax({
                    url: "<?php echo e(url('agents')); ?>/" + id,
                    method: "PUT",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editAgentModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', response.success, 'success');
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            $('.error-' + key).text(errors[key][0]);
                        }
                    }
                });
            });

            // Delete Agent
            $(document).on('click', '.delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?php echo e(url('agents')); ?>/" + id,
                            method: 'DELETE',
                            data: {
                                _token: '<?php echo e(csrf_token()); ?>' // Include CSRF token
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', response.success, 'success');
                            }
                        });
                    }
                });
            });
        });
    </script>
   <script>
       $(document).on('click', '.view-details', function() {
           var agentId = $(this).data('id');

           // Dynamically set the form action
           var url = "<?php echo e(route('agents.tickets.page', ':agent')); ?>";
           url = url.replace(':agent', agentId);
           $('#viewTicketsForm').attr('action', url);

           // Set agent ID in hidden input
           $('#form_agent_id').val(agentId);

           // Show the modal
           $('#ticketModal').modal('show');
       });


   </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\testing_project\resources\views/admin/agents/index.blade.php ENDPATH**/ ?>