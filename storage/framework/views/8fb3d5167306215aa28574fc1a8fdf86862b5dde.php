
<?php $__env->startSection('title','Customer Management'); ?>
<?php $__env->startSection('style'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <?php echo commonStyles(); ?>

 <?php echo customDatatableCss(); ?>

 <?php echo select2CssCustom(); ?>

<style>

    .file-card {
        display: flex;
        align-items: center;
        justify-content: center;
        /*height: 180px;*/
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 15px;
        overflow: hidden;
        background: #f9f9f9;
        transition: transform 0.2s ease-in-out;
        cursor: pointer;
    }
    .file-card:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .file-card img {
        /*max-height: 100%;*/
        max-width: 100%;
        object-fit: cover; /* ✅ images same ratio */
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Customer Management</h1>

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
        <div class="container-fluid">
        <div class="row">
                <!-- category create form -->

          <!-- end category create form -->
                  <div class="col-md-12">

                  <div class="card">



                       <!-- /.box-header -->
                      <div class="">
                          <br>
                          <div>
                              <button class="btn btn-dark mb-3" style="float: right;margin-right: 20px;" data-toggle="modal" data-target="#addCustomerModal">+ Add Customer</button>
                             </div>
                          <div class="card-body">

                              <div class="table-responsive"> <!-- Add this wrapper for scrollable tables -->
                                  <table id="teamsTable" class="table table-bordered table-striped">
                                      <thead>
                                      <tr>
                                          <th>Service#</th>
                                          <th>Name</th>
                                          <th>Email</th>
                                          <th>Phone</th>
                                          <th>Referrence</th>
                                          <th>Total Members</th>
                                           <th>Pnr</th>
                                          <!-- Uncomment the file column if needed -->
                                          <!-- <th>File</th> -->
                                          <th>Actions</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>


                      </div>
                  </div>
              </div>
          </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <!-- Edit Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document"> <!-- medium size -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addForm" method="POST" enctype="multipart/form-data">
                 <?php echo csrf_field(); ?>

                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>First Name</label>
                                <input type="text" name="firstName" class="form-control" value="<?php echo e(old('firstName')); ?>" required autocomplete="off" placeholder="Enter First Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Last Name</label>
                                <input type="text" name="lastName" class="form-control" value="<?php echo e(old('lastName')); ?>" required autocomplete="off" placeholder="Enter Last Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required autocomplete="off" placeholder="Enter Email">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Phone Number</label>
                                <input type="text" name="phoneNumber" class="form-control" placeholder="Enter Phone Number">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Total Members</label>
                                <input type="number" name="total_member" class="form-control" value="<?php echo e(old('total_member')); ?>" required autocomplete="off" placeholder="Total Members">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Reference </label>
                                <input type="text" name="pnr" class="form-control" placeholder="Enter Reference ">
                            </div>
                            <div class="form-group col-md-6">
                                <label>NIE</label>
                                <input type="text" name="id_card" class="form-control" placeholder="Enter  Customer NIE">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Passport No</label>
                                <input type="text" name="passport_no" class="form-control" placeholder="Enter  Customer Passport No">
                            </div>
                            <div class="form-group col-md-6">
                                <label>PNR</label>
                                <input type="text" name="customer_pnr" class="form-control" placeholder="Enter Reference Customer PNR">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Upload File</label>
                                <input type="file" name="file" class="form-control-file">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="submit" class="btn btn-primary">Add Customer</button>
                        <a href="#" id="clearAll" class="text-info font-weight-bold">Clear All</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editCustomerForm" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="modal-body">
                        <input type="hidden" id="editCustomerId" name="id">

                        <div class="form-group">
                            <label for="editName">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="editPhone">Phone</label>
                            <input type="text" class="form-control" id="editPhone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="editPnr">Reference</label>
                            <input type="text" class="form-control" id="editPnr" name="pnr" required>
                        </div>

                        <div class="form-group">
                            <label for="editTotal">Total Members</label>
                            <input type="number" class="form-control" id="editTotal" name="total_customer" required>
                        </div>
                        <div class="form-group">
                            <label for="editTotal">Pnr</label>
                            <input type="text" class="form-control" id="editCustomerPnr" name="customer_pnr" required>
                        </div>
                        <div class="form-group">
                            <label for="editTotal">NIE</label>
                            <input type="text" class="form-control" id="editIdCard" name="editId_card" required>
                        </div>
                        <div class="form-group">
                            <label for="editTotal">Passport No</label>
                            <input type="text" class="form-control" id="editPassportNo" name="editPassport_no" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<!-- Image Preview Modal -->


   <!-- Image Preview Modal -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document"> <!-- extra large modal -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Customer Files</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" id="filesContainer">
                        <!-- Files dynamically load here -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="fileUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="fileUploadForm" enctype="multipart/form-data">
                <input type="hidden" name="team_id" id="fileUploadTeamId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Files</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="files[]" id="fileInput" class="form-control" multiple required>

                        <!-- Preview Area -->
                        <div id="previewArea" class="mt-3 d-flex flex-wrap"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php echo commonScripts(); ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
          $('#teamsTable').DataTable({
            processing: true, // Show loading indicator
            scrollX: true,
            ordering: false,
            ajax: {
                url: '/teams/all', // Controller URL
                type: 'GET',
                dataSrc: 'data' // Use 'data' key from JSON response
            },
            columns: [
                { data: 'service_number' },
                { data: 'name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'pnr' },
                { data: 'total_customer' },
                { data: 'customer_pnr' },
                  {
                        data: null,
                        render: function(data, type, row) {
                            let authUserId = <?php echo e(auth()->id()); ?>; // Laravel user ID

                            let editDeleteButtons = '';
                            if (authUserId == 1) {
                                editDeleteButtons = `
                <button class="btn btn-warning btn-sm action-btn editCustomer"
                    data-id="${row.id}"
                    data-name="${row.name}"
                    data-email="${row.email}"
                    data-phone="${row.phone}"
                    data-pnr="${row.pnr}"
                    data-customerpnr="${row.customer_pnr}"
                    data-total="${row.total_customer}"
data-id_card="${row.id_card}"
data-passport="${row.passport_no}"
>
                    Edit
                </button>
                <button class="btn btn-danger btn-sm action-btn deleteCustomer" data-id="${row.id}">
                   Trash
                </button>
            `;
                            } else if (authUserId == 2) {
                                editDeleteButtons = `
                <button class="btn btn-warning btn-sm action-btn editCustomer"
                    data-id="${row.id}"
                    data-name="${row.name}"
                    data-email="${row.email}"
                    data-phone="${row.phone}"
                    data-pnr="${row.pnr}"
                    data-customerpnr="${row.customer_pnr}"
                    data-total="${row.total_customer}">
                    data-id_card="${row.id_card}"
data-passport="${row.passport_no}">
                    Edit
                </button>
            `;
                            }

                            let viewFileButton = `
            <button class="btn btn-primary btn-sm action-btn viewFilesBtn"
                data-customer="${row.id}">
                View Files
            </button>
        `;

                            let addFileButton = `
            <button class="btn btn-success btn-sm action-btn addFile" data-id="${row.id}">
                Add File
            </button>
        `;

                            return `
            ${editDeleteButtons}
            <a href="/tickets-detail/${row.id}" class="btn btn-info btn-sm action-btn viewTicket" data-id="${row.id}">
                View
            </a>
            ${viewFileButton}
            ${addFileButton}
        `;
                        }
                    }
            ]
        });
     $(document).on('click', '.viewFilesBtn', function() {
                let customerId = $(this).data('customer');

                // ✅ AJAX call
                $.ajax({
                    url: '/getImage',   // 👈 apna Laravel route yahaan lagao
                    type: 'GET',
                    data: { customer_id: customerId },
                    success: function(response) {
    let files = response.files;
    let html = '';

    if (files && files.length > 0) {
        files.forEach(function(fileObj) {
            if (fileObj.file_path) {
                let baseUrl = window.location.origin;
                // Encode karein taake space aur special chars issue na dein
                let encodedPath = encodeURIComponent(fileObj.file_path).replace(/%2F/g, "/");
                let fileUrl = `${baseUrl}/public/${encodedPath}`;

                // console.log("File URL:", fileUrl);

                let fileExtension = fileObj.file_path.split('.').pop().toLowerCase();


                html += `<div class="col-md-3 col-sm-4 col-6">
                            <div class="file-card" onclick="window.open('${fileUrl}','_blank')">`;

                if (['jpg','jpeg','png','gif','bmp','webp'].includes(fileExtension)) {
                    console.log("Image File:", fileUrl);
                    html += `<img src="${fileUrl}" alt="Image" style="width:100%;  object-fit:cover; border-radius:8px;">`;
                } else if (fileExtension === 'pdf') {
                    html += `<div class="d-flex flex-column align-items-center justify-content-center text-danger" style="height:180px; border:1px solid #ccc; border-radius:8px;">
                                <i class="fas fa-file-pdf fa-3x"></i>
                                <span>PDF</span>
                             </div>`;
                } else {
                    html += `<div class="d-flex flex-column align-items-center justify-content-center text-secondary" style="height:180px; border:1px solid #ccc; border-radius:8px;">
                                <i class="fas fa-file fa-3x"></i>
                                <span>File</span>
                             </div>`;
                }

                html += `</div></div>`;
            }
        });
    } else {
        html = `<div class="col-12"><p class="text-muted">No files available for this customer.</p></div>`;
    }

    $('#filesContainer').html(`<div class="row">${html}</div>`);
    $('#filePreviewModal').modal('show');
}

,
                    error: function(xhr) {
                        $('#filesContainer').html(`<p class="text-danger">Error loading files.</p>`);
                        $('#filePreviewModal').modal('show');
                    }
                });
            });


            // Add File button click
            // Open Add File Modal
            $(document).on('click', '.addFile', function() {
                let teamId = $(this).data('id');
                $('#fileUploadTeamId').val(teamId);
                $('#fileInput').val(""); // reset file input
                $('#previewArea').html(""); // reset previews
                $('#fileUploadModal').modal('show');
            });

// Preview multiple files
            $('#fileInput').on('change', function() {
                $('#previewArea').html("");
                let files = this.files;

                for (let i = 0; i < files.length; i++) {
                    let file = files[i];
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        let fileType = file.type;

                        if (fileType.startsWith('image/')) {
                            $('#previewArea').append(`
                    <div class="m-2">
                        <img src="${e.target.result}" width="100" class="img-thumbnail">
                    </div>
                `);
                        } else if (fileType === "application/pdf") {
                            $('#previewArea').append(`
                    <div class="m-2">
                        <embed src="${e.target.result}" type="application/pdf" width="100" height="100">
                    </div>
                `);
                        } else {
                            $('#previewArea').append(`
                    <div class="m-2 p-2 border">
                        <span>${file.name}</span>
                    </div>
                `);
                        }
                    };

                    reader.readAsDataURL(file);
                }
            });

// Submit Upload Form
            $('#fileUploadForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: '/teams/upload-files',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        alert(response.message);
                        $('#fileUploadModal').modal('hide');
                        $('#teamsTable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);
                    }
                });
            });
// Delete functionality
        $(document).on('click', '.deleteCustomer', function() {
            var teamId = $(this).data('id');

            if (confirm("Are you sure you want to delete this team?")) {
                $.ajax({
                    url: '/teams/delete/' + teamId, // Make sure this route exists in your backend
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, // CSRF token for Laravel
                    success: function(response) {
                        alert(response.message);
                        $('#teamsTable').DataTable().ajax.reload(); // Reload the table after deletion
                    },
                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);
                    }
                });
            }
        });
    });
    $(document).on('click', '.editCustomer', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        const pnr = $(this).data('pnr');
        const id_card = $(this).data('id_card');
        const passportNo = $(this).data('passport');
        const total = $(this).data('total');
        const customer_pnr = $(this).data('customerpnr');

        // Populate modal form fields
        $('#editCustomerId').val(id);
        $('#editName').val(name);
        $('#editEmail').val(email);
        $('#editPhone').val(phone);
        $('#editPnr').val(pnr);
        $('#editTotal').val(total);
        $('#editIdCard').val(id_card);
        $('#editPassportNo').val(passportNo);
        $('#editCustomerPnr').val(customer_pnr);

        // Show the modal
        $('#editCustomerModal').modal('show');
    });

    // Submit form with AJAX
    $(document).ready(function () {
    $('#addForm').on('submit', function (e) {
        e.preventDefault();


        // Always setup CSRF before AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var formData = new FormData(this); // Initialize FormData properly

        $.ajax({
            type: 'POST',
            url: baseUrl + '/teams',
            data: formData,
            contentType: false, // don't set content-type manually
            processData: false, // don't process the data
            cache: false,
            dataType: 'json',
            success: function (response) {

              $('#addCustomerModal').modal('hide');
                if (response.success) {

                    toastr.success(response.success);
                    $('#addForm')[0].reset();
                    $('#addCustomerModal').modal('hide');
                    getData('teamsTable');
                } else {

                          $('#addCustomerModal').modal('show');
                    toastr.error(response.data || 'Something went wrong');
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                $.LoadingOverlay("hide");
                $('#addCustomerModal').modal('show');

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error("An unexpected error occurred.");
                }

                console.error(xhr);
            }
        });
    });
});
    $('#editCustomerForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#editCustomerId').val();
        const formData = $(this).serialize();

        $.ajax({
            url: `/teams/update/${id}`, // Update to your update route
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status) {
                    $('#editCustomerModal').modal('hide');
                    $('#teamsTable').DataTable().ajax.reload(); // Reload DataTable
                    alert('Customer updated successfully.');
                } else {
                    alert('Failed to update customer.');
                }
            },
            error: function(xhr) {
                alert('Something went wrong.');
            }
        });
    });


</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script  src="<?php echo e(asset('js/teams/list.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/teams/listing.blade.php ENDPATH**/ ?>