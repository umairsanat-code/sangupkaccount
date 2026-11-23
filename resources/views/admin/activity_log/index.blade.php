@extends('layouts.master')

@section('title', 'Activity Log')

@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    .badge-action { font-size: 0.75rem; padding: 5px 10px; border-radius: 20px; }
    .stat-card { border-radius: 14px; padding: 18px 22px; color: #fff; }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid pt-3">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h4 class="mb-0 fw-bold">
                        <i class="fa fa-history me-2 text-primary"></i> Activity Log Report
                    </h4>
                    <small class="text-muted">Track user login, logout, and all system activities</small>
                </div>
                {{-- RESET BUTTON --}}
                <button id="resetLogBtn" class="btn btn-danger">
                    <i class="fa fa-trash-alt me-1"></i> Reset Log
                    <small class="d-block" style="font-size:10px;font-weight:400;">(Keep today only)</small>
                </button>
            </div>
        </div>

        {{-- ===== FILTERS ===== --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" id="filterStart" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" id="filterEnd" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">User</label>
                        <select id="filterUser" class="form-control">
                            <option value="">All Users</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->firstName }} {{ $u->lastName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Action</label>
                        <select id="filterAction" class="form-control">
                            <option value="">All Actions</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="ticket_add">Ticket Add</option>
                            <option value="status_change">Status Change</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="applyFilter" class="btn btn-primary w-100">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TABLE ===== --}}
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Activity Records</h5>
                <span class="badge bg-primary" id="totalCount">Loading…</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="activityTable" class="table table-bordered table-hover" width="100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
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
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    // Set default date range = today
    const today = new Date().toISOString().split('T')[0];
    $('#filterStart').val(today);
    $('#filterEnd').val(today);

    var table = $('#activityTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        ajax: {
            url: "{{ route('activity_log.data') }}",
            data: function (d) {
                d.start_date  = $('#filterStart').val();
                d.end_date    = $('#filterEnd').val();
                d.user_id     = $('#filterUser').val();
                d.action      = $('#filterAction').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'date_time',   name: 'created_at' },
            { data: 'user_name',   name: 'user_name' },
            { data: 'action_badge',name: 'action', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'ip_address',  name: 'ip_address' },
        ],
        drawCallback: function (settings) {
            var json = settings.json;
            if (json && json.recordsTotal !== undefined) {
                $('#totalCount').text('Total: ' + json.recordsTotal);
            }
        }
    });

    // Apply filter button
    $('#applyFilter').on('click', function () {
        table.ajax.reload();
    });

    // Reset Log button
    $('#resetLogBtn').on('click', function () {
        if (!confirm('⚠️ Are you sure?\n\nThis will DELETE all activity logs EXCEPT today\'s records.\n\nThis action CANNOT be undone!')) {
            return;
        }

        $.ajax({
            url: "{{ route('activity_log.reset') }}",
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            beforeSend: function () {
                $('#resetLogBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Resetting…');
            },
            success: function (response) {
                toastr.success(response.message);
                table.ajax.reload();
            },
            error: function () {
                toastr.error('Something went wrong. Please try again.');
            },
            complete: function () {
                $('#resetLogBtn').prop('disabled', false).html('<i class="fa fa-trash-alt me-1"></i> Reset Log <small class="d-block" style="font-size:10px;font-weight:400;">(Keep today only)</small>');
            }
        });
    });

});
</script>
@endsection
