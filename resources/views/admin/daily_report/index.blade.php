@extends('layouts.master')

@section('content')

    <div class="content-wrapper">
        <div class="container-fluid">

            {{-- ================= SUMMARY GRID ================= --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body">

                    <div class="row text-center fw-bold text-white" id="summaryGrid">
                        @if(Auth::user()->email === 'qa@techswivel.com')

                        <div class="col-md-3 mb-3">
                            <div class="p-3 rounded text-white shadow" style="background: linear-gradient(45deg, #e52d27, #b31217);">
                                <div class="fw-bold">PURCHASES</div>
                                <h4 class="mb-0" id="purchase">0</h4>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="p-3 rounded text-white shadow" style="background: linear-gradient(45deg, #56ab2f, #a8e063);">
                                <div class="fw-bold">TOTAL SALE</div>
                                <h4 class="mb-0" id="sale">0</h4>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="p-3 rounded text-white shadow" style="background: linear-gradient(45deg, #f7971e, #ffd200);">
                                <div class="fw-bold text-dark">Client PENDING</div>
                                <h4 class="mb-0 text-dark" id="pending">0</h4>
                            </div>
                        </div>

                            <div class="col-md-3 mb-3">
                                <div class="p-3 rounded text-white shadow" id="profitBox" style="background: linear-gradient(45deg, #8a2387, #e94057, #f27121);">
                                    <div class="fw-bold">GROSS PROFIT (Sale - Purchase)</div>
                                    <h4 class="mb-0" id="profit">0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="p-3 rounded text-white shadow" style="background: linear-gradient(45deg, #c0392b, #e74c3c);">
                                    <div class="fw-bold">TOTAL EXPENSES</div>
                                    <h4 class="mb-0" id="expenseTotal">0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="p-3 rounded text-white shadow" id="netProfitBox" style="background: linear-gradient(45deg, #232526, #414345);">
                                    <div class="fw-bold">NET PROFIT (After Expense)</div>
                                    <h4 class="mb-0" id="netProfit">0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="p-3 rounded text-white shadow"
                                     style="background: linear-gradient(45deg, #1e3c72, #2a5298);">
                                    <div class="fw-bold">TOTAL CARD</div>
                                    <h4 class="mb-0" id="cardTotal">0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="p-3 rounded text-white shadow"
                                     style="background: linear-gradient(45deg, #d38312, #a83279);">
                                    <div class="fw-bold">TOTAL BANK</div>
                                    <h4 class="mb-0" id="bankTotal">0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="p-3 rounded text-white shadow"
                                     style="background: linear-gradient(45deg, #11998e, #38ef7d);">
                                    <div class="fw-bold">TOTAL CASH</div>
                                    <h4 class="mb-0" id="cashTotal">0</h4>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="p-3 rounded text-white shadow"
                                     style="background: linear-gradient(45deg, #11998e, #38ef7d);">
                                    <div class="fw-bold">TOTAL PAYMENTS RECEIVED</div>
                                    <h4 class="mb-0" id="cashTotalAmount">0</h4>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            {{-- ================= REMAINING PAYMENT CLEAR (Super Admin Only) ================= --}}
            @if(auth()->id()=='1')
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <a href="javascript:void(0)" onclick="openClearPaymentModal()" class="text-decoration-none">
                        <div class="p-3 rounded text-white shadow d-flex justify-content-between align-items-center"
                             style="background: linear-gradient(45deg, #11998e, #38ef7d); cursor: pointer;">
                            <div>
                                <div class="fw-bold"><i class="fas fa-hand-holding-usd"></i> REMAINING PAYMENT CLEAR</div>
                                <h4 class="mb-0" id="clearPaymentTotal">0.00</h4>
                                <small>Pending: <span id="pendingClearTotal">0.00</span></small>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark p-2">
                                    <i class="fas fa-list"></i> View Payment Tickets
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endif

            {{-- ================= MAIN CARD ================= --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daily Sale Tickets</h4>
                    <a href="{{ route('daily_report.create') }}" class="btn btn-primary">
                        + Add New Ticket
                    </a>
                </div>

                <div class="card-body">

                    {{-- DATE FILTER --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label>Start Date</label>
                            <input type="date" id="start_date" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>End Date</label>
                            <input type="date" id="end_date" class="form-control">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button id="filterBtn" class="btn btn-success w-40 me-2">
                                Filter
                            </button>
                            <a href="javascript:void(0)" id="exportExcelBtn" class="btn btn-info w-30 me-2 text-white">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                            <a href="javascript:void(0)" id="exportPdfBtn" class="btn btn-danger w-30">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>

                    {{-- TABS --}}
                    <ul class="nav nav-tabs mb-3" id="statusTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="issue-tab" data-toggle="tab" href="#issueTab" role="tab">
                                <i class="fas fa-check-circle text-success"></i> Issue
                                <span class="badge bg-success ms-1" id="issueCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="reissue-tab" data-toggle="tab" href="#reissueTab" role="tab">
                                <i class="fas fa-redo text-warning"></i> Re-Issue
                                <span class="badge bg-warning text-dark ms-1" id="reissueCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="booked-tab" data-toggle="tab" href="#bookedTab" role="tab">
                                <i class="fas fa-ticket-alt text-danger"></i> Booked
                                <span class="badge bg-danger ms-1" id="bookedCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="cancel-tab" data-toggle="tab" href="#cancelTab" role="tab">
                                <i class="fas fa-times-circle text-info"></i> Cancel
                                <span class="badge bg-info ms-1" id="cancelCount">0</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="statusTabContent">

                        {{-- ISSUE TAB --}}
                        <div class="tab-pane fade show active" id="issueTab" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover dt-table" id="issueTable" data-status="issue" width="100%">
                                    <thead class="table-success">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time Limit</th>
                                        <th>Pax Name</th>
                                        <th>PNR</th>
                                        <th>System</th>
                                        <th>Issued Price</th>
                                        <th>Sale Price</th>
                                        <th>Pending</th>
                                        <th>Added By</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- RE-ISSUE TAB --}}
                        <div class="tab-pane fade" id="reissueTab" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover dt-table" id="reissueTable" data-status="reissue" width="100%">
                                    <thead class="table-warning">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time Limit</th>
                                        <th>Pax Name</th>
                                        <th>PNR</th>
                                        <th>System</th>
                                        <th>Issued Price</th>
                                        <th>Sale Price</th>
                                        <th>Pending</th>
                                        <th>Added By</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- BOOKED TAB --}}
                        <div class="tab-pane fade" id="bookedTab" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover dt-table" id="bookedTable" data-status="booked" width="100%">
                                    <thead class="table-danger">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time Limit</th>
                                        <th>Pax Name</th>
                                        <th>PNR</th>
                                        <th>System</th>
                                        <th>Issued Price</th>
                                        <th>Sale Price</th>
                                        <th>Pending</th>
                                        <th>Added By</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- CANCEL TAB --}}
                        <div class="tab-pane fade" id="cancelTab" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover dt-table" id="cancelTable" data-status="cancel" width="100%">
                                    <thead class="table-info">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time Limit</th>
                                        <th>Pax Name</th>
                                        <th>PNR</th>
                                        <th>System</th>
                                        <th>Issued Price</th>
                                        <th>Sale Price</th>
                                        <th>Pending</th>
                                        <th>Added By</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ================= DETAIL MODAL ================= --}}
    <div class="modal fade" id="ticketDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Ticket Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="ticketDetailContent">
                        {{-- Details will be populated here --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= CASH PAYMENT MODAL ================= --}}
    <div class="modal fade" id="cashPaymentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add Cash Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="cashPaymentForm">
                        @csrf
                        <input type="hidden" name="ticket_id" id="cash_ticket_id">
                        <input type="hidden" name="payment_method" value="Cash">

                        <div class="mb-3">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-money-bill"></i> Save Cash Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= BANK/CARD PAYMENT MODAL ================= --}}
    <div class="modal fade" id="bankCardPaymentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Bank/Card Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bankCardPaymentForm">
                        @csrf
                        <input type="hidden" name="ticket_id" id="bankcard_ticket_id">

                        <div class="mb-3">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="">Select Payment Method</option>
                                <option value="Bank">Bank Transfer</option>
                                <option value="Card">Credit/Debit Card</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Reference/Transaction ID</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                        </div>

                        <div class="mb-3">
                            <label>Bank Name (if Bank Transfer)</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="Optional">
                        </div>

                        <div class="mb-3">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-credit-card"></i> Save Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= HISTORY MODAL ================= --}}
    <div class="modal fade" id="historyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Payment History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="historyContent"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= PAYMENT CLEAR MODAL (Super Admin Only) ================= --}}
    @if(auth()->id()=='1')
    <div class="modal fade" id="clearPaymentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-hand-holding-usd"></i> Payment Clear Tickets
                        <small id="clearPaymentRange" class="ml-2"></small>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="clearPaymentTable" width="100%">
                            <thead class="thead-dark">
                            <tr>
                                <th>Date</th>
                                <th>Pax Name</th>
                                <th>PNR</th>
                                <th>Sale Price</th>
                                <th>Paid</th>
                                <th>Pending</th>
                                <th>Status</th>
                                <th>Payments</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endsection

@section('script')

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {

            const today = new Date().toISOString().split('T')[0];
            $('#start_date').val(today);
            $('#end_date').val(today);

            var baseUrl = "{{ route('daily_report.index') }}";

            // Initialize all 4 tables
            var tables = {};
            var tabStatuses = ['issue', 'reissue', 'booked', 'cancel'];
            var statusCounts = { issue: 0, reissue: 0, booked: 0, cancel: 0 };

            tabStatuses.forEach(function(status) {
                tables[status] = $('#' + status + 'Table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: baseUrl,
                        data: function(d) {
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                            d.status = status;
                        }
                    },
                    columns: [
                        {data: 'date'},
                        {data: 'time_limit'},
                        {data: 'pax_name'},
                        {data: 'pnr'},
                        {data: 'system'},
                        {data: 'issued_price'},
                        {data: 'sale_price'},
                        {data: 'pendings'},
                        {data: 'added_by'},
                        {data: 'action', orderable: false, searchable: false}
                    ],
                    drawCallback: function(settings) {
                        var json = settings.json;
                        statusCounts[status] = json.recordsTotal || 0;
                        $('#' + status + 'Count').text(statusCounts[status]);

                        // Update summary on first table draw
                        if (json.summary) {
                            function parseAmount(val) {
                                return parseFloat((val || '0').toString().replace(/,/g, '')) || 0;
                            }

                            $('#purchase').text(json.summary.purchase);
                            $('#sale').text(json.summary.sale);
                            $('#pending').text(json.summary.pending);
                            $('#vendorPaid').text(json.summary.vendorPaid);
                            $('#vendorBalance').text(json.summary.vendorBalance);
                            $('#profit').text(json.summary.grossProfit || json.summary.profit);
                            $('#expenseTotal').text(json.summary.expense || '0.00');
                            $('#netProfit').text(json.summary.netProfit || '0.00');

                            $('#cardTotal').text(json.summary.cardTotal);
                            $('#bankTotal').text(json.summary.bankTotal);
                            $('#cashTotal').text(json.summary.cashTotal);

                            let totalAmount =
                                parseAmount(json.summary.cashTotal) +
                                parseAmount(json.summary.bankTotal) +
                                parseAmount(json.summary.cardTotal);
                            $('#cashTotalAmount').text(totalAmount.toFixed(2));

                            $('#vPurchase').text(json.summary.vendorPurchase);
                            $('#vPaid').text(json.summary.vendorPaidTotal);
                            $('#vRemaining').text(json.summary.vendorRemaining);
                            $('#vOld').text(json.summary.vendorOld);
                            $('#vPending').text(json.summary.vendorPending);

                            $('#clearPaymentTotal').text(json.summary.clearPaymentTotal || '0.00');
                            $('#pendingClearTotal').text(json.summary.pendingClearTotal || '0.00');

                            const grossValue = parseAmount(json.summary.grossProfit || json.summary.profit);
                            const netValue = parseAmount(json.summary.netProfit);

                            if (grossValue >= 0) {
                                $('#profitBox').css('background', 'linear-gradient(45deg, #11998e, #38ef7d)');
                            } else {
                                $('#profitBox').css('background', 'linear-gradient(45deg, #e52d27, #b31217)');
                            }

                            if (netValue >= 0) {
                                $('#netProfitBox').css('background', 'linear-gradient(45deg, #11998e, #38ef7d)');
                            } else {
                                $('#netProfitBox').css('background', 'linear-gradient(45deg, #e52d27, #b31217)');
                            }
                        }
                    }
                });
            });

            // Reload all tables on tab show (in case they weren't loaded)
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                var statusMap = {
                    '#issueTab': 'issue',
                    '#reissueTab': 'reissue',
                    '#bookedTab': 'booked',
                    '#cancelTab': 'cancel'
                };
                var status = statusMap[target];
                if (status && tables[status]) {
                    tables[status].ajax.reload(null, false);
                }
            });

            // Filter button
            $('#filterBtn').click(function(){
                tabStatuses.forEach(function(status) {
                    tables[status].ajax.reload();
                });
            });

            $('#exportExcelBtn').click(function(){
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var url = "{{ route('daily_report.export_ticket_excel') }}";
                if(start_date && end_date) {
                    url += "?start_date="+start_date+"&end_date="+end_date;
                }
                window.location.href = url;
            });

            $('#exportPdfBtn').click(function(){
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();
                var url = "{{ route('daily_report.export_ticket_pdf') }}";
                if(start_date && end_date) {
                    url += "?start_date="+start_date+"&end_date="+end_date;
                }
                window.location.href = url;
            });

        });

        // VIEW TICKET DETAILS
        $(document).on('click', '.view-details', function(){
            var ticketId = $(this).data('id');

            $.ajax({
                url: "{{ url('/ticket/details') }}/" + ticketId,
                method: "GET",
                success: function(response){
                    var html = '';

                    // Ticket Info
                    html += '<div class="col-md-6">';
                    html += '<div class="card mb-3">';
                    html += '<div class="card-header bg-light"><strong>Ticket Information</strong></div>';
                    html += '<div class="card-body">';
                    html += '<p><strong>Ticket Number:</strong> ' + (response.ticket_number ? response.ticket_number : 'N/A') + '</p>';
                    html += '<p><strong>Type:</strong> ' + (response.type ? response.type : 'N/A') + '</p>';
                    html += '<p><strong>Status:</strong> <span class="badge bg-' +
                        (response.status === 'booked' ? 'danger' :
                            (response.status === 'issue' ? 'success' :
                                (response.status === 'reissue' ? 'warning' : 'info'))) + '">' +
                        (response.status ? response.status.toUpperCase() : 'N/A') + '</span></p>';
                    html += '<p><strong>Remarks:</strong> ' + (response.remarks ? response.remarks : 'N/A') + '</p>';
                    html += '</div></div></div>';

                    // Additional Info
                    html += '<div class="col-md-6">';
                    html += '<div class="card mb-3">';
                    html += '<div class="card-header bg-light"><strong>Additional Information</strong></div>';
                    html += '<div class="card-body">';
                    html += '<p><strong>Created At:</strong> ' + (response.created_at ? response.created_at : 'N/A') + '</p>';
                    html += '<p><strong>Updated At:</strong> ' + (response.updated_at ? response.updated_at : 'N/A') + '</p>';
                    html += '</div></div></div>';

                    // Payments Table
                    if(response.payments && response.payments.length > 0){
                        html += '<div class="col-12">';
                        html += '<div class="card">';
                        html += '<div class="card-header bg-light"><strong>Payment History</strong></div>';
                        html += '<div class="card-body">';
                        html += '<div class="table-responsive">';
                        html += '<table class="table table-sm table-bordered">';
                        html += '<thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Card Name</th><th>Stage</th><th>Remarks</th></tr></thead><tbody>';

                        $.each(response.payments, function(index, payment){
                            html += '<tr>';
                            html += '<td>' + payment.payment_date + '</td>';
                            html += '<td>' + payment.amount + '</td>';
                            html += '<td>' + payment.payment_method + '</td>';
                            html += '<td>' + (payment.card_name ? payment.card_name : '-') + '</td>';
                            html += '<td><span class="badge bg-primary">' + payment.payment_stage + '</span></td>';
                            html += '<td>' + (payment.remarks ? payment.remarks : '-') + '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table>';
                        html += '</div></div></div></div>';
                    }

                    $('#ticketDetailContent').html(html);
                    $('#ticketDetailModal').modal('show');
                },
                error: function(){
                    toastr.error('Error loading ticket details');
                }
            });
        });
        // OPEN CASH PAYMENT MODAL
        $(document).on('click', '.add-payment', function(){
            $('#cash_ticket_id').val($(this).data('id'));
            $('#cashPaymentModal').modal('show');
        });

        // OPEN BANK/CARD PAYMENT MODAL
        $(document).on('click', '.add-bankcard-payment', function(){
            $('#bankcard_ticket_id').val($(this).data('id'));
            $('#bankCardPaymentModal').modal('show');
        });

        // SAVE CASH PAYMENT
        $('#cashPaymentForm').submit(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{ route('ticket.payment.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response){
                    toastr.success('Cash Payment Added Successfully');
                    $('#cashPaymentModal').modal('hide');
                    $('#cashPaymentForm')[0].reset();
                    reloadAllTables();
                },
                error: function(xhr){
                    toastr.error(xhr.responseJSON?.message || 'Error adding payment');
                }
            });
        });

        // SAVE BANK/CARD PAYMENT
        $('#bankCardPaymentForm').submit(function(e){
            e.preventDefault();

            $.ajax({
                url: "{{ route('ticket.payment.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(response){
                    toastr.success('Bank/Card Payment Added Successfully');
                    $('#bankCardPaymentModal').modal('hide');
                    $('#bankCardPaymentForm')[0].reset();
                    reloadAllTables();
                },
                error: function(xhr){
                    toastr.error(xhr.responseJSON?.message || 'Error adding payment');
                }
            });
        });

        // VIEW PAYMENT HISTORY
        $(document).on('click', '.view-history', function(){
            var ticketId = $(this).data('id');

            $.get("{{ url('/ticket/payment-history') }}/"+ticketId, function(data){
                $('#historyContent').html(data);
                $('#historyModal').modal('show');
            });
        });

        // STATUS DROPDOWN HANDLER
        $(document).on('change', '.status-select', function() {
            var $el = $(this);
            var ticketId = $el.data('id');
            var newStatus = $el.val();
            var previousStatus = $el.data('previous') || newStatus;

            if (!confirm('Change status to ' + newStatus.toUpperCase() + '?')) {
                $el.val(previousStatus);
                return;
            }

            $.ajax({
                url: "{{ route('daily_report.update_status') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: ticketId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        $el.data('previous', newStatus);
                        toastr.success(response.message);
                        reloadAllTables();
                    } else {
                        $el.val(previousStatus);
                        toastr.error('Error updating status.');
                    }
                },
                error: function() {
                    $el.val(previousStatus);
                    toastr.error('Server error updating status.');
                }
            });
        });

        $(document).on('focus', '.status-select', function() {
            $(this).data('previous', $(this).val());
        });

        // ADD TICKET NUMBER
        $(document).on('click', '.add-ticket', function(){
            var ticketId = $(this).data('id');
            var ticketNumber = prompt('Enter Ticket Number:');
            if (ticketNumber) {
                $.ajax({
                    url: "{{ route('daily_report.addTicket') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: ticketId,
                        ticket_number: ticketNumber
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            reloadAllTables();
                        } else {
                            toastr.error('Error adding ticket number.');
                        }
                    },
                    error: function() {
                        toastr.error('Server error.');
                    }
                });
            }
        });

// Reload all tables
        function reloadAllTables() {
            var tabStatuses = ['issue', 'reissue', 'booked', 'cancel'];
            tabStatuses.forEach(function(status) {
                $('#' + status + 'Table').DataTable().ajax.reload(null, false);
            });
        }

        // OPEN PAYMENT CLEAR MODAL
        $(document).on('click', '.view-clear-payment', function(){
            openClearPaymentModal();
        });

        // PAYMENT CLEAR MODAL (show tickets with payments in selected date range)
        $(document).on('click', '[data-target="#clearPaymentModal"], #clearPaymentBox', function(){
            openClearPaymentModal();
        });

        function openClearPaymentModal() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            var today = new Date().toISOString().split('T')[0];
            if (!startDate) startDate = today;
            if (!endDate) endDate = today;

            $('#clearPaymentRange').text('[' + startDate + ' to ' + endDate + ']');

            if ($.fn.DataTable.isDataTable('#clearPaymentTable')) {
                $('#clearPaymentTable').DataTable().destroy();
            }
            $('#clearPaymentTable tbody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
            $('#clearPaymentModal').modal('show');

            $.ajax({
                url: "{{ route('payment.clear.tickets') }}",
                method: "GET",
                data: { start_date: startDate, end_date: endDate },
                success: function (rows) {
                    $('#clearPaymentTable tbody').html('');
                    $('#clearPaymentTable').DataTable({
                        data: rows,
                        destroy: true,
                        order: [[0, 'desc']],
                        columns: [
                            { data: 'date' },
                            { data: 'pax_name' },
                            { data: 'pnr' },
                            { data: 'sale_price' },
                            { data: 'paid_amount' },
                            { data: 'pending' },
                            {
                                data: 'status',
                                render: function (data) {
                                    var cls = data === 'issue' ? 'success' : (data === 'booked' ? 'danger' : (data === 'reissue' ? 'warning' : 'info'));
                                    return '<span class="badge bg-' + cls + '">' + (data ? data.toUpperCase() : 'N/A') + '</span>';
                                }
                            },
                            {
                                data: 'payments',
                                render: function (payments) {
                                    if (!payments || payments.length === 0) return '-';
                                    var html = '<ul class="list-unstyled mb-0" style="font-size:12px;">';
                                    $.each(payments, function (i, p) {
                                        html += '<li>[' + p.payment_date + '] ' + p.payment_method + ' ' + p.amount +
                                            (p.payment_stage ? ' (<b>' + p.payment_stage + '</b>)' : '') +
                                            (p.card_name ? ' - ' + p.card_name : '') + '</li>';
                                    });
                                    html += '</ul>';
                                    return html;
                                }
                            }
                        ]
                    });
                },
                error: function () {
                    $('#clearPaymentTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading tickets.</td></tr>');
                }
            });
        }
    </script>

@endsection
