@extends('layouts.master')


@section('content')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Tickets Management</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
    <div class="container mt-4">

<div class="p-4 bg-white shadow rounded-lg mb-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Filter Tickets</h2>
                <div class="row">
                    <!-- Departure City -->
                    <div class="col-md-2 mb-3">
                        <label for="departure_city" class="form-label">Departure City</label>
                        <input type="text" id="departure_city" class="form-control" placeholder="Enter Departure City">
                    </div>
                     <div class="col-md-2 mb-3">
                        <label for="issue_date" class="form-label">Departure Start Date</label>
                        <input type="date" id="issue_start_date" class="form-control" placeholder="Enter Issue Date">
                    </div>
                         <div class="col-md-2 mb-3">
                        <label for="issue_date" class="form-label">Departure End Date</label>
                        <input type="date" id="issue_end_date" class="form-control" placeholder="Enter Issue Date">
                    </div>

                    <!-- Airline -->
                    <div class="col-md-2 mb-3">
                        <label for="airline_id" class="form-label">Airline</label>
                        <select id="airline_id" class="form-control">
                            <option value="">Select Airline</option>
                            @foreach ($airlines as $airline)
                                <option value="{{ $airline->id }}">{{ $airline->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Ticket Number -->
                    <div class="col-md-2 mb-3">
                        <label for="ticket_number" class="form-label">Ticket Number</label>
                        <input type="text" id="ticket_number" class="form-control" placeholder="Enter Ticket Number">
                    </div>
                    <!-- Action Buttons -->
                    <div class="col-md-2 d-flex align-items-center mt-1">
                        <div>
                            <button id="filterTickets" class="btn btn-primary me-2">Filter</button>
                            <button id="clearFilters" class="btn btn-secondary">Clear</button>
                        </div>
                    </div>
                </div>
            </div>
        <!--<a href="{{ route('tickets.create') }}" class="btn btn-success mb-3">Create New Ticket</a>-->
        <table id="tickets-table" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Ticket Number</th>
                <th>Pax Name</th>
                <th>Airline</th>
                <th>Flight Date</th>
                <th>Departure</th>
                <th>Destination</th>
                <th>Pnr</th>
                <th>Status</th>
                <!--<th>Actions</th>-->
            </tr>
            </thead>
        </table>
    </div>
    </div>
    </div>

@endsection
@section('script')
            <!-- Add DataTables initialization script -->

            <!-- Include DataTables JS & jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
            
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>

            <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.min.js"></script>

            <script>
                $(document).ready(function() {
                   const table = $('#tickets-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('tickets.index') }}',
                            data: function (d) {
                                d.departure_city = $('#departure_city').val();
                                    // d.issue_date = $('#issue_date').val();
                                       d.issue_start_date = $('#issue_start_date').val();
                                d.issue_end_date = $('#issue_end_date').val();
                                // Add departure_city filter
                                d.airline_id = $('#airline_id').val();               // Add airline filter
                                d.ticket_number = $('#ticket_number').val();   // Add ticket_number filter
                            }
                        },
                            // Route that serves DataTable
                        columns: [
                            { data: 'id', name: 'id' },
                            { data: 'ticket_number', name: 'ticket_number' },
                            { data: 'customer', name: 'customer' },
                            { data: 'airline', name: 'airline' },
                            { data: 'flight_date', name: 'flight_date' },
                            { data: 'departure_city', name: 'departure_city' },
                            { data: 'destination_city', name: 'destination_city' },
                           {data:'pnr',name:'pnr'},
                            { data: 'status', name: 'status' },
                            // { data: 'actions', name: 'actions', orderable: false, searchable: false },
                        ],
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'csvHtml5',
                                text: 'CSV Export',
                                title: 'Tickets for SanguTravel & Tour'
                            },
                            {
                                extend: 'excelHtml5',
                                text: 'Excel Export',
                                title: 'Tickets for SanguTravel & Tour'
                            },
                            {
                                extend: 'pdfHtml5',
                                text: 'PDF Export',
                                title: 'Tickets for SanguTravel & Tour',
                                customize: function (doc) {
                                    doc.content[1].table.widths = ['*', '*', '*', '*', '*']; // Equal column widths
                                }
                            },
                            {
                                extend: 'print',
                                text: 'Print',
                                title: 'Tickets for SanguTravel & Tour',
                                customize: function (win) {
                                    $(win.document.body).find('table').addClass('table table-bordered');
                                }
                            }
                        ],
                    });
                      // Filter event
                    $('#filterTickets').on('click', function () {
                        table.draw(); // Redraw the table with new filters
                    });

                    // Clear filters
                    $('#clearFilters').on('click', function () {
                        $('#departure_city').val('');
                        $('#airline_id').val('');
                        $('#ticket_number').val('');
                        $('#issue_date').val('');
                        table.draw(); // Redraw the table without filters
                    });
                });
            </script>
@endsection
