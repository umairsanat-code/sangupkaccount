@extends('layouts.master')
@section('title', 'Buyers')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.css">
<style>
    .user-card-header {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        background-size: cover;
        background-position: center;
        position: relative;
        padding: 35px 15px;
    }

    /* Burgundy transparent overlay */
    .user-card-header::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(94, 55, 53, 0.85);
    }

    /* Ensure text stays above overlay */
    .user-card-header * {
        position: relative;
        z-index: 2;
    }

    /* Avatar style */
    .user-avatar {
        width: 80px;
        height: 80px;
        border: 3px solid #fff;
        transition: .3s;
    }

    .user-avatar:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 15px rgba(94,55,53,0.6);
    }

    .description-header {
        font-size: 18px;
    }

    .description-text {
        font-size: 12px;
        letter-spacing: .5px;
    }

    .btn {
        border-radius: 10px;
    }

</style>

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="mb-2 row">
                    <div class="col-sm-6">
                        <h1>App Users</h1>
                    </div>
                    <div class="col-sm-6" >
                        {{--        <button class="btn btn-primary" style="float: right">Add New Member</button>--}}
                        <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addMemberModal">Add New Member</button>

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @foreach ($users as $user)
                        <div class="col-md-4 mb-4">
                            <div class="card card-widget widget-user shadow-lg border-0 rounded-xl">

                                <!-- Header with background image + overlay -->
{{--                                <div class="widget-user-header text-white text-center user-card-header">--}}
                                <div class="widget-user-header  text-white text-center" style="background-color: #28a745 !important">
                                    <h3 class="widget-user-username font-weight-bold mb-1" style="font-size:20px;">
                                        {{ $user->name }}
                                    </h3>
                                    <h5 class="widget-user-desc text-light" style="font-size:14px; font-style: italic;">
                                        {{ $user->role }}
                                    </h5>
                                </div>

                                <div class="widget-user-image text-center mt-2">
                                    <img class="img-circle elevation-3 user-avatar"
                                         src="{{ asset('/img/default-avatar.svg') }}"
                                         alt="User Avatar">
                                </div>

                                <div class="card-footer">
                                    <div class="row text-center">

                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header font-weight-bold">{{ $user->total_sales }}</h5>
                                                <span class="description-text text-muted">SALES</span>
                                            </div>
                                        </div>

                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header font-weight-bold">{{ $user->total_profit }}</h5>
                                                <span class="description-text text-muted">EARNING</span>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header font-weight-bold">{{ $user->total_tickets }}</h5>
                                                <span class="description-text text-muted">TICKETS</span>
                                            </div>
                                        </div>

                                    </div>

                                    <hr>

                                    <div class="d-flex justify-content-between">

                                        <button class="btn btn-sm btn-outline-secondary editUserBtn"
                                                data-id="{{ $user->id }}"
                                                data-first_name="{{ $user->firstName }}"
                                                data-last_name="{{ $user->lastName }}"
                                                data-email="{{ $user->email }}"
                                                data-toggle="modal" data-target="#editUserModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <button class="btn btn-sm status-toggle-btn
                        {{ $user->status == 'Active' ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-id="{{ $user->id }}"
                                                data-url="{{ route('members.toggleStatus', $user->id) }}">
                                            <i class="fas {{ $user->status == 'Active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            {{ $user->status == 'Active' ? 'Banned' : 'Activate' }}
                                        </button>

                                        <a href="{{ route('user.pending.tickets', $user->id) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            Pending
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>
        </section>
        <!-- /.content -->


        <div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form id="memberForm" method="POST" action="{{ route('members.store') }}" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Member</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body row">
                        <!-- Form Fields -->
                        <div class="col-sm-6 form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Member</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>

            </div>
        </div>


        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" id="editMemberForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name="user_id" id="edit-user-id">

                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="firstName" id="edit-first-name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="lastName" id="edit-last-name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" id="edit-email" class="form-control" required>
                            </div>
                            <div class=" form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Pending Tickets Modal -->
        <!-- Pending Tickets Modal -->
                <div class="modal fade" id="pendingTicketModal" tabindex="-1" role="dialog" aria-labelledby="pendingTicketModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content shadow-lg rounded-lg">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title font-weight-bold">Pending Tickets Summary</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <!-- Total Pending -->
                        <div class="mb-3 text-right">
                            <h5>Total Pending Amount: <span id="totalPendingAmount" class="text-danger font-weight-bold">0</span> €</h5>
                        </div>

                        <!-- Tickets Table -->
                        <div class="table-responsive">
                            <table id="pendingTicketsTable" class="table table-hover table-striped table-bordered">
                                <thead class="thead-dark">
  <tr>
            <th>#</th>
            <th>Ticket Number</th>
            <th>Purchase Price</th>
            <th>Sale Price</th>
            <th>Profit</th>
            <th>Pending Amount</th>
        </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>



        @endsection

      @section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize DataTable safely
            const table = $('#pendingTicketsTable').DataTable({
                destroy: true, // ensures reinitialization doesn't break
                processing: true,
                responsive: true,
                autoWidth: false
            });

            // Handle Edit button
            $('.editUserBtn').click(function () {
                $('#edit-user-id').val($(this).data('id'));
                $('#edit-first-name').val($(this).data('first_name'));
                $('#edit-last-name').val($(this).data('last_name'));
                $('#edit-email').val($(this).data('email'));

                // Set form action URL
                let id = $(this).data('id');
                $('#editMemberForm').attr('action', `/members/${id}`);
            });

            // Handle Status Toggle
            $('.status-toggle-btn').click(function () {
                const url = $(this).data('url');
                const button = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to change the status!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {_token: '{{ csrf_token() }}'}, function (response) {
                            location.reload();
                        });
                    }
                });
            });

            // Pending Tickets Modal
            $('.pendingTicketBtn').click(function () {
                const userId = $(this).data('id');
                const modal = $('#pendingTicketModal');
                const tbody = $('#pendingTicketsTable tbody');
                let totalPending = 0;

                $.ajax({
                    url: `/members/${userId}/pending-tickets`,
                    type: 'GET',
                    success: function (data) {
                        tbody.empty();
                        table.clear().draw(); // Clear existing DataTable

                        if (data.tickets.length) {
                            data.tickets.forEach((ticket, index) => {
                                const pendingAmount = parseFloat(ticket.sale_price) - parseFloat(ticket.purchase_price);
                                totalPending += pendingAmount;

                                const row = [
                                    index + 1,
                                    ticket.ticket_number,
                                    ticket.purchase_price + " €",
                                    ticket.sale_price + " €",
                                    ticket.profit + " €",
                                    pendingAmount.toFixed(2) + " €"
                                ];

                                table.row.add(row).draw(false); // Use row.add() to safely handle the data
                            });
                        } else {
                            tbody.append(`<tr><td colspan="6" class="text-center">No Pending Tickets</td></tr>`);
                        }

                        $('#totalPendingAmount').text(totalPending.toFixed(2));
                        modal.modal('show');
                    },
                    error: function () {
                        Swal.fire('Error', 'Could not fetch ticket data.', 'error');
                    }
                });
            });

            // Handle Add Member Form
            $('#memberForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let formData = form.serialize();

                let password = form.find('input[name="password"]').val();
                let confirmPassword = form.find('input[name="password_confirmation"]').val();

                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Passwords do not match!'
                    });
                    return;
                }

                $.post(url, formData, function (response) {
                    $('#addMemberModal').modal('hide');
                    Swal.fire('Success', 'Member added successfully!', 'success').then(() => {
                        location.reload();
                    });
                }).fail(function (xhr) {
                    Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong!', 'error');
                });
            });

            // Handle Edit Form
            $('#editMemberForm').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const url = form.attr('action');
                const formData = form.serialize();

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    success: function () {
                        $('#editUserModal').modal('hide');
                        Swal.fire('Updated', 'User updated successfully!', 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Could not update user.', 'error');
                    }
                });
            });
        });
    </script>
@endsection

