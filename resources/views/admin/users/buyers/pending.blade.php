@extends('layouts.master')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <div class="card">
                <div class="card-header bg-gradient-success text-white">
                    <h5>Airline Management</h5>
                </div>
                <div class="card-body">
                    <h3>Pending Tickets Summary</h3>
                    <p>
                        Total Pending Amount:
                        <strong>{{ number_format($totalPendingAmount, 2) }} €</strong>
                    </p>

                    <table id="pendingTicketsTable" class="table table-bordered table-striped" width="100%">
                        <thead class="bg-success text-white">
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Ticket Number</th>
                            <th>Issue Price</th>
                            <th>Sale Price</th>
                            <th>Paid Amount</th>
                            <th>Pending Amount</th>
                            <th>Pnr</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')

    <!-- ✅ DataTables + Buttons CDN Links -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- ✅ Export Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- ✅ Initialize DataTable -->
    <script>
        $(document).ready(function () {
            $('#pendingTicketsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.pending.tickets', $userId) }}",
                columns: [
                    {
                        data: null,
                        name: 'rownum',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'customer', name: 'customer' },
                    { data: 'ticket_number', name: 'ticket_number' },
                    { data: 'issue_price', name: 'issue_price' },
                    { data: 'sale_price', name: 'sale_price' },
                    { data: 'paid', name: 'paid' },
                    { data: 'pending', name: 'pending' },
                    { data: 'sale_date', name: 'sale_date' },
                ],
                dom: 'Bfrtip', // 👈 Required for buttons
                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '⬇️ Download CSV',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '🖨️ Print Table',
                        className: 'btn btn-primary btn-sm'
                    }
                ],
                language: {
                    processing: "<span class='text-success fw-bold'>Loading data...</span>"
                }
            });
        });
    </script>

    <!-- ✅ Optional Button Spacing -->
    <style>
        .dt-buttons {
            margin-bottom: 15px;
        }
    </style>

@endsection
