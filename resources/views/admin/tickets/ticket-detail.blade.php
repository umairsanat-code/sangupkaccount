@extends('layouts.master')
@section('title', 'Ticket Details')

@section('styles')
    <style>
        body { font-family: 'Nunito', sans-serif; }
        .glass-card { background: rgba(255,255,255,0.1); border-radius: 12px; padding: 15px; margin-bottom: 20px; }
        .section-title { font-weight: 600; margin-bottom: 15px; }
        .stat-cards { display: flex; gap: 10px; flex-wrap: wrap; }
        .stat-card { flex: 1; background: rgba(0,0,0,0.1); border-radius: 8px; padding: 10px; text-align: center; }
        .stat-label { font-size: 0.9rem; color: #ccc; }
        .stat-value { font-size: 1.2rem; font-weight: 600; }
        .table-warning { background-color: #fff3cd; }
        .empty-state { text-align: center; padding: 50px 0; color: #aaa; }
        .pagination-container { text-align: center; margin-top: 20px; }
        .btn-action-group button { margin-right: 5px; }
        #activePagination > span,
        #refundedPagination > span {
            display: none !important;
        }
        /* Pax Ticket Card */
        .pax-ticket-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .pax-ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }

        .pax-ticket-title {
            font-weight: 700;
            font-size: 1.35rem;
            margin-bottom: 20px;
            color: #222;
            border-bottom: 2px solid rgba(0,0,0,0.05);
            padding-bottom: 8px;
        }

        .pax-stat-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .pax-stat-card {
            flex: 1 1 45%;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }

        .pax-stat-card:hover {
            background: rgba(255,255,255,0.1);
        }

        .pax-stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #555;
        }

        .pax-stat-value {
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* (Optional) Hide all inner pagination if you want only custom pagination via JS */
        #activePagination .relative.z-0.inline-flex.shadow-sm.rounded-md,
        #refundedPagination .relative.z-0.inline-flex.shadow-sm.rounded-md {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h1>Ticket Detail</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#ticketFormModal">
                            <i class="fas fa-plus mr-1"></i> Add Ticket
                        </button>
                        <button onclick="generateInvoice({{ $customer->id }})" class="btn btn-sm btn-secondary">
                            <i class="fas fa-file-invoice mr-1"></i> Generate Invoice
                        </button>
                        <button onclick="finalGenerateInvoice({{ $customer->id }})" class="btn btn-sm btn-tertiary">
                            <i class="fas fa-file-invoice mr-1"></i> Final Invoice
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <!-- Left: Pax Details -->
                    <div class="col-md-6">
                        <div class="glass-card">
                            <h3 class="section-title text-dark">Pax Details</h3>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Pax Name</label>
                                    <input type="text" class="form-control" readonly value="{{ $customer->name }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Pax Email</label>
                                    <input type="text" class="form-control" readonly value="{{ $customer->email }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Pax Reference</label>
                                    <input type="text" class="form-control" readonly value="{{ $customer->pnr }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Service Number</label>
                                    <input type="text" class="form-control" readonly value="{{ $customer->service_number }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Price Summary -->
                    <div class="col-md-6">
                        <div class="row">

                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-tags"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sale Price</span>
                                        <span class="info-box-number">€{{ number_format($totalSalePrice,2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Issue Price</span>
                                        <span class="info-box-number">€{{ number_format($totalPurchasePrice,2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending Amount</span>
                                        <span class="info-box-number">€{{ number_format($totalPendingAmount,2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-secondary">
                                    <span class="info-box-icon"><i class="fas fa-undo"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Refund Amount</span>
                                        <span class="info-box-number">€{{ number_format($refundedAMount,2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="info-box {{ $totalProfit>=0 ? 'bg-success' : 'bg-danger' }}">
                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Profit</span>
                                        <span class="info-box-number">€{{ number_format($totalProfit,2) }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>


                <!-- Tickets Tabs -->
                <div class="glass-card">
                    <nav>
                        <div class="nav nav-tabs" id="ticketTabs" role="tablist">
                            <a class="nav-item nav-link active" id="active-tab" data-toggle="tab" href="#activeTickets">Active Tickets
                                <span class="badge badge-primary">{{ $tickets->total() }}</span>
                            </a>
                            <a class="nav-item nav-link" id="refunded-tab" data-toggle="tab" href="#refundedTickets">Refunded Tickets
                                <span class="badge badge-secondary">{{ $refundedTickets->total() }}</span>
                            </a>
                        </div>
                    </nav>
                    <div class="tab-content p-3">
                        <!-- Active Tickets -->
                        <div class="tab-pane fade show active" id="activeTickets">
                            <div id="activeTicketsContainer">
                                @include('admin.tickets.partials.active_ajax', ['tickets'=>$tickets])
                            </div>
                            <ul class="pagination justify-content-center mt-3" id="activePagination"></ul>
                        </div>

                        <!-- Refunded Tickets -->
                        <div class="tab-pane fade" id="refundedTickets">
                            <div id="refundedTicketsContainer">
                                @include('admin.tickets.partials.refunded_ajax', ['refundedTickets'=>$refundedTickets])
                            </div>
                            <ul class="pagination justify-content-center mt-3" id="refundedPagination"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('admin.tickets.partials.add')
    @include('admin.tickets.partials.edit')
    @include('admin.tickets.partials.payment_history')
{{--    @include('admin.tickets.partials.payment_modal')--}}
{{--    @include('admin.tickets.partials.refund_modal')--}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="/payment-clear" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">Clear Payment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Hidden Inputs -->
                                <input type="hidden" name="customer_id" id="modalCustomerId">
                                <input type="hidden" name="ticket_id" id="modalTicketId">

                                <!-- Ticket Number (Readonly) -->
                                <div class="form-group">
                                    <label for="ticketNumber">Ticket Number</label>
                                    <input type="text" id="modalTicketNumber" class="form-control" readonly>
                                </div>

                                <!-- Pending Amount (Readonly) -->
                                <div class="form-group">
                                    <label for="pendingAmount">Pending Amount</label>
                                    <input type="text" id="modalPendingAmount" class="form-control" readonly>
                                </div>

                                <!-- Payment Amount -->
                                <div class="form-group">
                                    <label for="paymentAmount">Payment Amount</label>
                                    <input type="text" id="paymentAmount" name="payment_amount" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="paymentDate">Payment Amount</label>
                                    <input type="date" id="paymentDate" name="payment_date" class="form-control" required>
                                </div>

                                <!-- Payment Type -->
                                <div class="form-group">
                                    <label for="paymentType">Payment Type</label>
                                    <select id="paymentType" name="payment_type" class="form-control" required>
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                        <option value="card">Card</option>
                                    </select>
                                </div>
                                 <div class="form-group">
                                    <label for="paymentDate">Payment Note</label>
                                    <textarea name="payment_note" class="form-control" rows="5" cols="40" ></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Submit Payment</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

    <div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('ticket.processRefund') }}" method="POST" id="refundForm">
                            @csrf
                            <form action="{{ route('ticket.processRefund') }}" method="POST" id="refundForm">
                            @csrf
                            <input type="hidden" name="ticket_id" id="ticketId">
                            <input type="hidden" name="sale_id" id="saleId">

                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="refundAmount">Refund Amount</label>
                                    <input type="number" id="refundAmount" name="refund_amount" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="serviceCharges">Service Charges</label>
                                    <input type="number" id="serviceCharges" name="service_charges" class="form-control" value="0" required>
                                    @error('airline_charges')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="airlineCharges">Airline Charges</label>
                                    <input type="number" id="airlineCharges" name="airline_charges" class="form-control" value="0" required>
                                    @error('airline_charges')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="amountToRefund">Amount to Refund</label>
                                    <input type="number" id="amountToRefund" name="amount_to_refund" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="refundReason">Refund Reason</label>
                                    <textarea id="refundReason" name="refund_reason" class="form-control" required></textarea>
                                    @error('refund_reason')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">Process Refund</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
@endsection

@section('script')
    <script>
        function generateInvoice(ticketId) {
                    window.open(`/tickets/${ticketId}/invoice`, '_blank');
                }
                function finalGenerateInvoice(ticketId) {
                    window.open(`/tickets/${ticketId}/final_invoice`, '_blank');
                }
        const customerId = {{ $customer->id }};

        // Load tickets via AJAX
        function loadTickets(type = 'active', page = 1) {
            let url = '';
            let data = {};

            if(type === 'active') {
                url = `/tickets/${customerId}/active-ajax`;
                data = { active_page: page };
            } else {
                url = `/tickets/${customerId}/refunded-ajax`;
                data = { refunded_page: page };
            }

            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                success: function(res){
                    if(type === 'active') {
                        $('#activeTicketsContainer').html(res.html);
                        $('#activePagination').html(res.pagination);
                    } else {
                        $('#refundedTicketsContainer').html(res.html);
                        $('#refundedPagination').html(res.pagination);
                    }

                },
                error: function(err){
                    console.error(err);
                    alert('Failed to load tickets.');
                }
            });
        }

        // Handle pagination link click
        $(document).on('click', '.pagination a', function(e){
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1] || 1;
            if($(this).closest('#activePagination').length){
                loadTickets('active', page);
            } else {
                loadTickets('refunded', page);
            }
        });

        $(document).ready(function(){
            // Initially load tickets
            loadTickets('active');
            loadTickets('refunded');

            // Tab click
            $('#ticketTabs a').on('click', function(e){
                e.preventDefault();
                $(this).tab('show');
                let tab = $(this).attr('id') === 'active-tab' ? 'active' : 'refunded';
                loadTickets(tab);
            });

            // Edit Ticket
            $(document).on('click', '.editTicketBtn', function () {
                let ticketId = $(this).data('id');
                $.get(`/tickets/${ticketId}/edit`, function(response){
                    if(response.success){
                        let data = response.data;
                        $('#edit_ticket_id').val(data.id);
                        $('#edit_ticket_number').val(data.ticket_number);
                        $('#edit_flight_date').val(data.flight_date);
                        $('#edit_airline_id').val(data.airline_id);
                        $('#edit_departure_city').val(data.departure_city);
                        $('#edit_destination_city').val(data.destination_city);
                        $('#edit_class').val(data.class);
                        $('#edit_status').val(data.status);
                        $('#edit_note').val(data.note);
                        $('#edit_refund_status').val(data.refund_status);
                        $('#edit_idCard').val(data.id_card);
                        $('#edit_passportno').val(data.passport_no);
                        $('#agent_id_select').val(data.agent_id).trigger('change');
                        if(data.sales.length>0){
                            let sale = data.sales[0];
                            $('#edit_sale_id').val(sale.id);
                            $('#edit_sale_price').val(sale.sale_price);
                            $('#edit_purchase_price').val(data.purchase_price);
                            let paymentStatus = (sale.payment_status||'').toLowerCase();
                            $('#edit_payment_checkbox').prop('checked', paymentStatus=='paid');
                            let paid = sale.payment_method || {};
                            $('#edit_amount_paid').val(paid.amount_paid||'');
                            $('#edit_payment_method').val(paid.payment_method||'');
                            $('#edit_bank_name').val(paid.bank_name||'');
                            $('#edit_card_number').val(paid.card_number||'');
                            $('#edit_payment_date').val(paid.payment_date||'');
                        }
                        $('#editTicketModal').modal('show');
                    }
                });
            });

            // Update Ticket
            $('#editTicketForm').submit(function(e){
                e.preventDefault();
                let ticketId = $('#edit_ticket_id').val();
                $.ajax({
                    url: `/tickets/${ticketId}`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(res){
                        if(res.success){
                            alert('Ticket updated successfully!');
                            $('#editTicketModal').modal('hide');
                            loadTickets('active');
                        } else {
                            alert('Failed to update ticket');
                        }
                    }
                });
            });

            // Payment Modal
            $(document).on('click', '.open-payment-modal', function(){
                let button = $(this);
                $('#modalTicketId').val(button.data('ticket-id'));
                $('#modalCustomerId').val(button.data('customer-id'));
                $('#modalTicketNumber').val(button.data('ticket-number'));
                $('#modalPendingAmount').val(button.data('pending-amount'));
            });

            // Refund Modal
            $(document).on('click', '.open-refund-modal', function(){
                let button = $(this);
                let salePrice = parseFloat(button.data('sale-price')||0);
                $('#ticketId').val(button.data('ticket-id'));
                $('#saleId').val(button.data('sale-id'));
                $('#refundAmount').val(salePrice.toFixed(2));
                $('#amountToRefund').val(salePrice.toFixed(2));

                $('#serviceCharges,#airlineCharges').on('input', function(){
                    let serviceCharges = parseFloat($('#serviceCharges').val()||0);
                    let airlineCharges = parseFloat($('#airlineCharges').val()||0);
                    let refundAmount = salePrice - (serviceCharges + airlineCharges);
                    $('#amountToRefund').val(refundAmount.toFixed(2));
                });
            });

            // Delete Ticket
            {{--$(document).on('click', '.deleteTicketBtn', function() {--}}
            {{--    let ticketId = $(this).data('id');--}}

            {{--    if (confirm('Are you sure you want to delete this ticket?')) {--}}
            {{--        $.ajax({--}}
            {{--            url: `/tickets-detail/${ticketId}`,   // ✅ Must match Laravel route--}}
            {{--            type: 'DELETE',--}}
            {{--            data: {--}}
            {{--                _token: '{{ csrf_token() }}'--}}
            {{--            },--}}
            {{--            success: function(res) {--}}
            {{--                alert('Ticket deleted successfully');--}}
            {{--                loadTickets('active');--}}
            {{--            },--}}
            {{--            error: function(xhr) {--}}
            {{--                console.error(xhr.responseText);--}}
            {{--                alert('Failed to delete ticket. Check console for details.');--}}
            {{--            }--}}
            {{--        });--}}
            {{--    }--}}
            {{--});--}}


            // Payment History
            function loadPaymentHistory(ticketId, page=1){
                $('#paymentHistoryContent').html('Loading...');
                $.get(`/tickets/${ticketId}/payment-history`, {page: page}, function(res){
                    if(res.success){
                        $('#ticketNumber').text(res.ticket_number);
                        $('#totalPaid').text(`€${res.total_paid}`);
                        $('#pendingAmount').text(`€${res.total_pending}`);
                        let tbody = $('#paymentHistoryTable').empty();
                        if(res.payments.length){
                            res.payments.forEach((p,i)=>{
                                tbody.append(`<tr>
                                <td>${i+1}</td>
                                <td>€${p.amount}</td>
                                <td>${p.method}</td>
                                <td>${p.notes}</td>
                                <td>${p.date}</td>
                            </tr>`);
                            });
                        } else {
                            tbody.html('<tr><td colspan="5">No payment history</td></tr>');
                        }
                    } else {
                        $('#paymentHistoryContent').html('Failed to load payment history');
                    }
                });
            }

            $(document).on('click', '.paymentHistoryTicketBtn', function(){
                loadPaymentHistory($(this).data('id'));
            });

        });
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
          $(document).ready(function () {
                    // Debugging: Check if jQuery is loaded
                    console.log("jQuery loaded");

                    // Form submission event
                    $('#ticketFormModal').on('shown.bs.modal', function () {
                        $('#ticketForm').off('submit').on('submit', function (e) {
                            e.preventDefault();
                            console.log("Dynamic AJAX function called");

                            // Clear previous error messages
                            $('.error-text').text('');

                            $.ajax({
                                url: '{{ route("tickets.store") }}',
                                method: 'POST',
                                data: $(this).serialize(),
                                success: function (response) {
                                    console.log(response);
                                    if (response.success) {
                                        $('#ticketFormModal').modal('hide');
                                        alert(response.message);
                                    }
                                },
                                error: function (xhr) {
                                    console.error(xhr);
                                    if (xhr.status === 422) {
                                        $.each(xhr.responseJSON.errors, function (key, value) {
                                            $('.' + key + '_error').text(value[0]);
                                        });
                                    }
                                }
                            });
                        });
                    });

                });
    </script>
    <!-- Include SweetAlert -->
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '.deleteTicketBtn', function() {
            const ticketId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This ticket will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form (Laravel-safe method)
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route("tickets.destroy", ":id") }}'.replace(':id', ticketId)
                    });

                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));

                    $('body').append(form);
                    form.submit();
                }
            });
        });
    </script>


@endsection




{{--@extends('layouts.master')--}}
{{--<style>--}}
{{--    .modal-header {--}}
{{--        background-color: #007bff;--}}
{{--        color: white;--}}
{{--        border-bottom: none;--}}
{{--    }--}}

{{--    .card-body h6 {--}}
{{--        font-size: 0.9rem;--}}
{{--        margin-bottom: 0.3rem;--}}
{{--        color: #6c757d;--}}
{{--    }--}}

{{--    .card-body h5 {--}}
{{--        font-size: 1.2rem;--}}
{{--        margin: 0;--}}
{{--    }--}}

{{--    .nav-tabs .nav-link.active {--}}
{{--        background-color: #007bff;--}}
{{--        color: white;--}}
{{--        font-weight: bold;--}}
{{--    }--}}
{{--    .table-warning {--}}
{{--        background-color: #fff3cd;--}}
{{--    }--}}
{{--    .bg-gray {--}}
{{--        background-color: #f8f9fa; /* Light gray */--}}
{{--        padding: 16px;--}}
{{--        border-radius: 8px;--}}
{{--        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);--}}
{{--    }--}}
{{--    /*.custom-table {*/--}}
{{--    /*    background-color: #f9f9f9;*/--}}
{{--    /*    border: 1px solid #dee2e6;*/--}}
{{--    /*    border-radius: 8px;*/--}}
{{--    /*    overflow: hidden;*/--}}
{{--    /*}*/--}}

{{--    /*.custom-table th,*/--}}
{{--    /*.custom-table td {*/--}}
{{--    /*    vertical-align: middle;*/--}}
{{--    /*    text-align: center;*/--}}
{{--    /*    padding: 12px 8px;*/--}}
{{--    /*}*/--}}






{{--    .badge-success {--}}
{{--        background-color: #28a745;--}}
{{--        color: #fff;--}}
{{--        padding: 5px 10px;--}}
{{--        border-radius: 5px;--}}
{{--        font-size: 14px;--}}
{{--        font-weight: bold;--}}
{{--    }--}}

{{--    /*.btn-sm i {*/--}}
{{--    /*    margin-right: 4px;*/--}}
{{--    /*}*/--}}

{{--    /*.table-hover tbody tr {*/--}}
{{--    /*    transition: background-color 0.3s ease;*/--}}
{{--    /*}*/--}}

{{--    path[fill-rule="evenodd"][clip-rule="evenodd"] {--}}
{{--        display: none;--}}
{{--    }--}}
{{--</style>--}}
{{--@section('content')--}}
{{--    <div class="content-wrapper">--}}
{{--        <!-- Content Header (Page header) -->--}}
{{--        <section class="content-header">--}}
{{--            <div class="container-fluid">--}}
{{--                <div class="row mb-2">--}}
{{--                    <div class="col-sm-6">--}}
{{--                        <h1>Ticket Detail</h1>--}}
{{--                    </div>--}}
{{--                    <div class="col-sm-6 text-right">--}}
{{--                        <!-- Add Buttons -->--}}
{{--                        <!-- Add Ticket Button -->--}}
{{--                      <!-- Add Ticket Button -->--}}
{{--<button --}}
{{--    class="btn btn-sm mr-2" --}}
{{--    style="background-color: #e0e0e0; color: #333; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;"--}}
{{--    data-toggle="modal" --}}
{{--    data-target="#ticketFormModal"--}}
{{-->--}}
{{--    <i class="fas fa-plus mr-2"></i> Add Ticket--}}
{{--</button>--}}

{{--<!-- Generate Invoice Button -->--}}
{{--<button --}}
{{--    onclick="generateInvoice({{ $customer->id }})" --}}
{{--    class="btn btn-sm float-right mr-3 ml-2" --}}
{{--    style="background-color: #f2f2f2; color: #333; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;"--}}
{{-->--}}
{{--    <i class="fas fa-file-invoice mr-2"></i> Generate Invoice--}}
{{--</button>--}}

{{--<!-- Final Invoice Button -->--}}
{{--<button --}}
{{--    onclick="finalGenerateInvoice({{ $customer->id }})" --}}
{{--    class="btn btn-sm float-right ml-0" --}}
{{--    style="background-color: #d6d6d6; color: #222; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;"--}}
{{-->--}}
{{--    <i class="fas fa-file-invoice mr-2"></i> Final Invoice--}}
{{--</button>--}}




{{--                    </div>--}}
{{--                </div>--}}
{{--            </div><!-- /.container-fluid -->--}}
{{--        </section>--}}
{{--        <!-- Main content -->--}}
{{--        <section class="content">--}}
{{--            <!-- Default box -->--}}
{{--            <div class="card card-solid">--}}
{{--                <div class="card-body">--}}
{{--                    <div class="row">--}}
{{--                        <!-- Customer Details Section -->--}}
{{--                        <div class="col-12 col-md-6">--}}
{{--                            <h3 class="my-4 font-bold text-2xl text-gray-800">Pax Detail Append</h3>--}}
{{--                            <div class="p-4 bg-white rounded-2xl shadow-lg">--}}
{{--                                <div class="row">--}}
{{--                                    <!-- Customer Name -->--}}
{{--                                    <div class="form-group col-md-6 mb-4">--}}
{{--                                        <label for="customerName" class="text-gray-700 font-medium">Pax Name</label>--}}
{{--                                        <input type="text" id="customerName" class="form-control border-gray-300 rounded-lg shadow-sm" readonly value="{{$customer->name}}">--}}
{{--                                    </div>--}}
{{--                                    <!-- Customer Email -->--}}
{{--                                    <div class="form-group col-md-6 mb-4">--}}
{{--                                        <label for="customerEmail" class="text-gray-700 font-medium">Pax Email</label>--}}
{{--                                        <input type="email" id="customerEmail" class="form-control border-gray-300 rounded-lg shadow-sm" readonly value="{{$customer->email}}">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="row">--}}
{{--                                    <!-- Customer PNR -->--}}
{{--                                    <div class="form-group col-md-6 mb-4">--}}
{{--                                        <label for="customerPnr" class="text-gray-700 font-medium">Pax Refence</label>--}}
{{--                                        <input type="text" id="customerPnr" class="form-control border-gray-300 rounded-lg shadow-sm" readonly value="{{$customer->pnr}}">--}}
{{--                                    </div>--}}
{{--                                    <!-- Service Number -->--}}
{{--                                    <div class="form-group col-md-6 mb-4">--}}
{{--                                        <label for="totalNumber" class="text-gray-700 font-medium">Service Number</label>--}}
{{--                                        <input type="number" id="totalNumber" class="form-control border-gray-300 rounded-lg shadow-sm" readonly value="{{$customer->service_number}}">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Pricing Details Section -->--}}
{{--                        <div class="col-12 col-md-6 ml-auto mt-6">--}}
{{--                            <h3 class="my-4 font-bold text-2xl text-gray-800">Pax Ticket Price detail</h3>--}}

{{--                            <div class="row">--}}
{{--                                <!-- Column 1: Sale Price and Purchase Price -->--}}
{{--                                <div class="col-6">--}}
{{--                                    <div class="p-4 bg-green-100 rounded-lg shadow mb-4">--}}
{{--                                        <small class="text-gray-500 font-medium">Sale Price</small>--}}
{{--                                        <p class="text-2xl font-bold text-green-600 mt-1">€{{ number_format($totalSalePrice, 2) }}</p>--}}
{{--                                    </div>--}}
{{--                                    <div class="p-4 bg-red-100 rounded-lg shadow mb-4">--}}
{{--                                        <small class="text-gray-500 font-medium">Issue Price</small>--}}
{{--                                        <p class="text-2xl font-bold text-red-600 mt-1">€{{ number_format($totalPurchasePrice, 2) }}</p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <!-- Column 2: Pending Amount and Profit -->--}}
{{--                                <div class="col-6">--}}
{{--                                    <div class="p-4 bg-yellow-400 rounded-lg shadow mb-4">--}}
{{--                                        <small class="text-danger-500 font-medium">Pending Amount</small>--}}
{{--                                        <p class="text-2xl font-bold text-yellow-500 mt-1">€{{ number_format($totalPendingAmount, 2) }}</p>--}}
{{--                                    </div>--}}
{{--                                    <div class="p-4 bg-gray-200 rounded-lg shadow">--}}
{{--                                        <small class="text-danger-500 font-medium" >Refund Amount</small>--}}
{{--                                        <p class="text-2xl font-bold text-yellow-600 mt-2">€{{ number_format($refundedAMount, 2) }}</p>--}}

{{--                                       --}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-6 col-sm-6 ml-5">--}}

{{--                                    <div class="p-4 bg-gray-200 rounded-lg shadow">--}}
{{--                                        <small class="text-gray-500 font-medium">Profit</small>--}}
{{--                                        <p class="text-2xl font-bold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">--}}
{{--                                            €{{ number_format($totalProfit, 2) }}--}}
{{--                                        </p>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}


{{--                    <div class=" py-2 px-3 mt-4">--}}
{{--                        <div class="row">--}}
{{--                            <!-- First div with 60% width -->--}}

{{--                            <!-- Second div with 40% width -->--}}
{{--                            <div class="col-12 col-md-12">--}}
{{--                                <div class="row mt-4">--}}
{{--                                    <nav class="w-100">--}}
{{--                                        <div class="nav nav-tabs" id="product-tab" role="tablist">--}}
{{--                                            <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab"--}}
{{--                                               aria-controls="product-desc" aria-selected="true">Tickets</a>--}}
{{--                                            <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab"--}}
{{--                                               aria-controls="product-comments" aria-selected="false">Refunded Tickets</a>--}}
{{--                                        </div>--}}
{{--                                    </nav>--}}
{{--                                    <div class="tab-content p-3" id="nav-tabContent">--}}
{{--                                        <!-- Non-Refunded Tickets -->--}}
{{--                                        <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab">--}}
{{--                                            <!-- Ticket Table -->--}}
{{--                                            <table class="table table-striped " id="custom-table">--}}
{{--                                                <thead>--}}
{{--                                                <tr>--}}
{{--                                                    <th>#</th>--}}
{{--                                                    <th>Ticket Number</th>--}}
{{--                                                     <th>Added By</th>--}}
{{--                                                    <th>Pax Name</th>--}}
{{--                                                    <th>Flight Date</th>--}}
{{--                                                     <th>Pnr</th>--}}
{{--                                                    <th>Status</th>--}}
{{--                                                    <th>Refund Status</th>--}}
{{--                                                    <th>Issue Price</th>--}}
{{--                                                    <th>Sale Price</th>--}}
{{--                                                    <td>Action</td>--}}
{{--                                                </tr>--}}
{{--                                                </thead>--}}
{{--                                                <tbody>--}}
{{--                                                @foreach ($tickets as $ticket)--}}

{{--                                                    @php--}}
{{--                                                        $pendingAmount = $ticket->sales->sum('pending_amount');--}}
{{--                                                    @endphp--}}
{{--                                                    <tr class="{{ $pendingAmount > 0 ? 'table-warning' : '' }}">--}}
{{--                                                        <td>{{ $loop->iteration }}</td>--}}
{{--                                                        <!-- Rest of the columns -->--}}


{{--                                                    <td>{{ $ticket->ticket_number }}</td>--}}
{{--                                                      <td>{{ $ticket->author_name }}</td>--}}
{{--                                                     <td>{{ $ticket->customer_name??$customer->name }}</td>--}}
{{--                                                        <td>{{ \Carbon\Carbon::parse($ticket->flight_date)->format('d-m-Y') }}</td>--}}
{{--                                                           <td>{{$ticket->pnr}}</td>--}}
{{--                                                       --}}
{{--                                                       <td>--}}
{{--    @if($ticket->status == 'Booked')--}}
{{--        Issue Ticket--}}
{{--    @elseif($ticket->status == 'Cancelled')--}}
{{--        Re-Issue Ticket--}}
{{--    @else--}}
{{--        {{ $ticket->status }}--}}
{{--    @endif--}}
{{--</td>--}}
{{--                                                        <td>{{ $ticket->refund_status }}</td>--}}
{{--                                                        <td>€{{ $ticket->purchase_price }}</td>--}}
{{--                                                        <td>€{{ $ticket->sales->sum('sale_price') }}</td>--}}
{{--                                                        <td>--}}
{{--    @if ($pendingAmount > 0)--}}
{{--        <button class="btn btn-outline-success btn-sm open-payment-modal"--}}
{{--                data-ticket-id="{{ $ticket->id }}"--}}
{{--                data-ticket-number="{{ $ticket->ticket_number }}"--}}
{{--                data-customer-id="{{ $customer->id }}"--}}
{{--                data-pending-amount="{{ $pendingAmount }}"--}}
{{--                data-toggle="modal"--}}
{{--                data-target="#paymentModal"--}}
{{--                title="Make Payment">--}}
{{--            <i class="fas fa-euro-sign"></i>--}}
{{--        </button>--}}
{{--    @else--}}
{{--        <span class="btn btn-outline-success btn-sm" title="Cleared">--}}
{{--            <i class="fas fa-check-circle"></i>--}}
{{--        </span>--}}
{{--    @endif--}}

{{--    <!-- Refund Button -->--}}
{{--    <button class="btn btn-outline-danger btn-sm open-refund-modal"--}}
{{--            data-ticket-id="{{ $ticket->id }}"--}}
{{--            data-sale-id="{{ $ticket->sales()->first()->id ?? '' }}"--}}
{{--            data-sale-price="{{ $ticket->sales()->first()->sale_price ?? 0 }}"--}}
{{--            data-toggle="modal"--}}
{{--            data-target="#refundModal"--}}
{{--            title="Refund">--}}
{{--        <i class="fas fa-undo"></i>--}}
{{--    </button>--}}
{{--@if(auth()->id()=='1'|| auth()->id()=='2')--}}
{{--    <!-- Edit Button -->--}}
{{--    <button class="btn btn-outline-primary btn-sm editTicketBtn"--}}
{{--            data-id="{{ $ticket->id }}"--}}
{{--            title="Edit Ticket">--}}
{{--        <i class="fas fa-edit"></i>--}}
{{--    </button>--}}
{{--    @endif--}}
{{--    <!-- Payment History Button -->--}}
{{--    <button class="btn btn-outline-info btn-sm paymentHistoryTicketBtn"--}}
{{--            data-id="{{ $ticket->id }}"--}}
{{--            data-toggle="modal"--}}
{{--            data-target="#paymentHistoryModal"--}}
{{--            title="Payment History">--}}
{{--        <i class="fas fa-history"></i>--}}
{{--    </button>--}}
{{--    @if(auth()->id()=='1')--}}
{{--    <!-- Delete Button -->--}}
{{--    <button class="btn btn-outline-danger btn-sm deleteTicketBtn"--}}
{{--            data-id="{{ $ticket->id }}"--}}
{{--            title="Delete Ticket">--}}
{{--        <i class="fas fa-trash"></i>--}}
{{--    </button>--}}
{{--    @endif--}}
{{--</td>--}}

{{--<script>--}}
{{--    document.addEventListener('DOMContentLoaded', function () {--}}
{{--        document.querySelectorAll('.deleteTicketBtn').forEach(function (button) {--}}
{{--            button.addEventListener('click', function () {--}}
{{--                const ticketId = this.dataset.id;--}}
{{--                if (confirm('Are you sure you want to delete this ticket?')) {--}}
{{--                    const form = document.createElement('form');--}}
{{--                    form.action = '{{ route("tickets.destroy", ":id") }}'.replace(':id', ticketId);--}}
{{--                    form.method = 'POST';--}}

{{--                    const csrfToken = document.createElement('input');--}}
{{--                    csrfToken.type = 'hidden';--}}
{{--                    csrfToken.name = '_token';--}}
{{--                    csrfToken.value = '{{ csrf_token() }}';--}}
{{--                    form.appendChild(csrfToken);--}}

{{--                    const methodField = document.createElement('input');--}}
{{--                    methodField.type = 'hidden';--}}
{{--                    methodField.name = '_method';--}}
{{--                    methodField.value = 'DELETE';--}}
{{--                    form.appendChild(methodField);--}}

{{--                    document.body.appendChild(form);--}}
{{--                    form.submit();--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}

{{--                                                    </tr>--}}
{{--                                                @endforeach--}}

{{--                                                </tbody>--}}

{{--                                            </table>--}}
{{--                                            <div class="d-flex justify-content-center">--}}
{{--                                                {{ $tickets->links() }}--}}
{{--                                            </div>--}}
{{--                                        </div>--}}



{{--                                        <!-- Refunded Tickets -->--}}
{{--                                        <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">--}}
{{--                                            <!-- Refund Ticket Table -->--}}
{{--                                            <table class="table table-striped">--}}
{{--                                                <thead>--}}
{{--                                                <tr>--}}
{{--                                                    <th>#</th>--}}
{{--                                                    <th>Ticket Number</th>--}}
{{--                                                    <th>Flight Date</th>--}}
{{--                                                    <th>Status</th>--}}
{{--                                                    <th>Refund Status</th>--}}
{{--                                                   <th>Issue Price</th>--}}
{{--                                                   <th>Refund Amount</th>--}}
{{--                                                --}}
{{--                                                    <th>Refund Charges</th>--}}
{{--                                                    <td>Action</td>--}}
{{--                                                </tr>--}}
{{--                                                </thead>--}}
{{--                                                <tbody>--}}
{{--                                                @foreach ($refundedTickets as $ticket)--}}
{{--                                                    <tr>--}}
{{--                                                        <td>{{ $loop->iteration }}</td>--}}
{{--                                                        <td>{{ $ticket->ticket_number }}</td>--}}
{{--                                                        <td>{{ $ticket->flight_date }}</td>--}}
{{--                                                        <td>{{ $ticket->status }}</td>--}}
{{--                                                        <td>{{ $ticket->refund_status }}</td>--}}
{{--                                                        <td>€{{ $ticket->sales->sum('sale_price') }}</td>--}}
{{--                                                        <td>€{{ $ticket->sales->sum('refund_amount') }}</td>--}}
{{--                                                        --}}
{{--                                                        <td>€{{ $ticket->sales->sum('service_charges') + $ticket->sales->sum('airline_charges') }}</td>--}}
{{--                                                        --}}
{{--                                                        <td>--}}
{{--                                                            <span class="btn btn-danger">Refunded</span>--}}
{{--                                                        </td>--}}
{{--                                                    </tr>--}}
{{--                                                @endforeach--}}
{{--                                                </tbody>--}}
{{--                                            </table>--}}
{{--                                            <!-- Pagination Links -->--}}
{{--                                            <!--{{ $refundedTickets->links() }}-->--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                            </div>--}}
{{--                           --}}
{{--                        </div>--}}

{{--                        </div>--}}

{{--                    </div>--}}


{{--            </div>--}}
{{--        </section>--}}
{{--        <!-- Add Ticket Button -->--}}

{{--    </div>--}}

{{--    <!-- Add Ticket Modal -->--}}
{{--    @include('admin.tickets.partials.add')--}}
{{--    @include('admin.tickets.partials.edit')--}}
{{--    @include('admin.tickets.partials.payment_history')--}}
{{--    <!-- Payment Modal -->--}}
{{--    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog">--}}
{{--            <div class="modal-content">--}}
{{--                <form action="/payment-clear" method="POST">--}}
{{--                    @csrf--}}
{{--                    <div class="modal-header">--}}
{{--                        <h5 class="modal-title" id="paymentModalLabel">Clear Payment</h5>--}}
{{--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                            <span aria-hidden="true">&times;</span>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body">--}}
{{--                        <!-- Hidden Inputs -->--}}
{{--                        <input type="hidden" name="customer_id" id="modalCustomerId">--}}
{{--                        <input type="hidden" name="ticket_id" id="modalTicketId">--}}

{{--                        <!-- Ticket Number (Readonly) -->--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="ticketNumber">Ticket Number</label>--}}
{{--                            <input type="text" id="modalTicketNumber" class="form-control" readonly>--}}
{{--                        </div>--}}

{{--                        <!-- Pending Amount (Readonly) -->--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="pendingAmount">Pending Amount</label>--}}
{{--                            <input type="text" id="modalPendingAmount" class="form-control" readonly>--}}
{{--                        </div>--}}

{{--                        <!-- Payment Amount -->--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="paymentAmount">Payment Amount</label>--}}
{{--                            <input type="text" id="paymentAmount" name="payment_amount" class="form-control" required>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="paymentDate">Payment Amount</label>--}}
{{--                            <input type="date" id="paymentDate" name="payment_date" class="form-control" required>--}}
{{--                        </div>--}}

{{--                        <!-- Payment Type -->--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="paymentType">Payment Type</label>--}}
{{--                            <select id="paymentType" name="payment_type" class="form-control" required>--}}
{{--                                <option value="cash">Cash</option>--}}
{{--                                <option value="bank">Bank</option>--}}
{{--                                <option value="card">Card</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                         <div class="form-group">--}}
{{--                            <label for="paymentDate">Payment Note</label>--}}
{{--                            <textarea name="payment_note" class="form-control" rows="5" cols="40" ></textarea>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="submit" class="btn btn-success">Submit Payment</button>--}}
{{--                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Refund Modal -->--}}
{{--    <div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <form action="{{ route('ticket.processRefund') }}" method="POST" id="refundForm">--}}
{{--                    @csrf--}}
{{--                    <form action="{{ route('ticket.processRefund') }}" method="POST" id="refundForm">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="ticket_id" id="ticketId">--}}
{{--                    <input type="hidden" name="sale_id" id="saleId">--}}

{{--                    <div class="modal-body">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="refundAmount">Refund Amount</label>--}}
{{--                            <input type="number" id="refundAmount" name="refund_amount" class="form-control" readonly>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="serviceCharges">Service Charges</label>--}}
{{--                            <input type="number" id="serviceCharges" name="service_charges" class="form-control" value="0" required>--}}
{{--                            @error('airline_charges')--}}
{{--                            <div class="text-danger">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="airlineCharges">Airline Charges</label>--}}
{{--                            <input type="number" id="airlineCharges" name="airline_charges" class="form-control" value="0" required>--}}
{{--                            @error('airline_charges')--}}
{{--                            <div class="text-danger">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="amountToRefund">Amount to Refund</label>--}}
{{--                            <input type="number" id="amountToRefund" name="amount_to_refund" class="form-control" readonly>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="refundReason">Refund Reason</label>--}}
{{--                            <textarea id="refundReason" name="refund_reason" class="form-control" required></textarea>--}}
{{--                            @error('refund_reason')--}}
{{--                            <div class="text-danger">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
{{--                        <button type="submit" class="btn btn-danger">Process Refund</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--@endsection--}}
{{--@section('script')--}}
{{--    <script>--}}
{{--        function generateInvoice(ticketId) {--}}
{{--            window.open(`/tickets/${ticketId}/invoice`, '_blank');--}}
{{--        }--}}
{{--        function finalGenerateInvoice(ticketId) {--}}
{{--            window.open(`/tickets/${ticketId}/final_invoice`, '_blank');--}}
{{--        }--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        $.ajaxSetup({--}}
{{--            headers: {--}}
{{--                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
{{--            }--}}
{{--        });--}}

{{--        $(document).ready(function () {--}}
{{--            // Debugging: Check if jQuery is loaded--}}
{{--            console.log("jQuery loaded");--}}

{{--            // Form submission event--}}
{{--            $('#ticketFormModal').on('shown.bs.modal', function () {--}}
{{--                $('#ticketForm').off('submit').on('submit', function (e) {--}}
{{--                    e.preventDefault();--}}
{{--                    console.log("Dynamic AJAX function called");--}}

{{--                    // Clear previous error messages--}}
{{--                    $('.error-text').text('');--}}

{{--                    $.ajax({--}}
{{--                        url: '{{ route("tickets.store") }}',--}}
{{--                        method: 'POST',--}}
{{--                        data: $(this).serialize(),--}}
{{--                        success: function (response) {--}}
{{--                            console.log(response);--}}
{{--                            if (response.success) {--}}
{{--                                $('#ticketFormModal').modal('hide');--}}
{{--                                alert(response.message);--}}
{{--                            }--}}
{{--                        },--}}
{{--                        error: function (xhr) {--}}
{{--                            console.error(xhr);--}}
{{--                            if (xhr.status === 422) {--}}
{{--                                $.each(xhr.responseJSON.errors, function (key, value) {--}}
{{--                                    $('.' + key + '_error').text(value[0]);--}}
{{--                                });--}}
{{--                            }--}}
{{--                        }--}}
{{--                    });--}}
{{--                });--}}
{{--            });--}}

{{--        });--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        // Edit Ticket Function (fetches data and opens modal)--}}
{{--        function editTicket(ticketId) {--}}
{{--            $.ajax({--}}
{{--                url: `/tickets/${ticketId}/edit`, // Backend route to fetch ticket data--}}
{{--                type: 'GET',--}}
{{--                success: function (response) {--}}
{{--                    if (response.success) {--}}
{{--                        $('#edit_ticket_id').val(response.data.id);--}}
{{--                        $('#edit_ticket_number').val(response.data.ticket_number);--}}
{{--                        $('#edit_flight_date').val(response.data.flight_date);--}}
{{--                        $('#edit_airline_id').val(response.data.airline_id);--}}
{{--                        $('#edit_departure_city').val(response.data.departure_city);--}}
{{--                        $('#edit_destination_city').val(response.data.destination_city);--}}
{{--                        $('#edit_class').val(response.data.class);--}}
{{--                        $('#edit_status').val(response.data.status);--}}
{{--                          $('#edit_note').val(response.data.note);--}}
{{--                        $('#edit_refund_status').val(response.data.refund_status);--}}
{{--                                                $('#agent_id_select').val(response.data.agent_id).trigger('change');--}}

{{--                        $('#edit_purchase_price').val(response.data.purchase_price);--}}
{{--                        $('#edit_sale_price').val(response.data.sales[0].sale_price);--}}
{{--                        if (response.data.sales && response.data.sales.length > 0) {--}}
{{--                            const sale = response.data.sales[0]; // Access the first sale--}}
{{--                            $('#edit_sale_id').val(sale.id);--}}

{{--                            const paymentMethod = sale.payment_method; // Access the payment method--}}

{{--                            // $('#edit_sales_id').val(data.sales.id);--}}
{{--                            $('#edit_sale_date').val(response.data.sales[0].sale_date);--}}
{{--                            $('#edit_payment_status').val(response.data.sales[0].payment_status);--}}
{{--                            const paymentStatus = response.data.sales[0].payment_status?.trim().toLowerCase(); // Normalize the value--}}
{{--                            console.log(paymentStatus,response.data.sales[0].payment_status);--}}

{{--                            if (paymentStatus === 'paid') {--}}
{{--                                console.log('paid');--}}
{{--                                $('#edit_payment_checkbox').prop('checked', true); // Check the checkbox--}}
{{--                            } else if (paymentStatus === 'pending') {--}}
{{--                                console.log('unpaid');--}}

{{--                                $('#edit_payment_checkbox').prop('checked', false); // Uncheck the checkbox--}}
{{--                            } else {--}}
{{--                                console.warn('Unexpected payment status:', paymentStatus); // Debugging output--}}
{{--                            }--}}

{{--                            // $('#edit_payment_status').val(response.data.sales[0].payment_status);--}}
{{--                            // $('#edit_sales_date').val(data.sales[0].sale_date);--}}
{{--                            var paid = paymentMethod;--}}

{{--// Check if `paid` is null or undefined--}}
{{--                            if (paid) {--}}
{{--                                console.log(paid);--}}
{{--                                $('#edit_amount_paid').val(paid.amount_paid || '');--}}
{{--                                $('#edit_payment_method').val(paid.payment_method || '');--}}
{{--                                $('#edit_bank_name').val(paid.bank_name || '');--}}
{{--                                $('#edit_card_number').val(paid.card_number || '');--}}
{{--                                $('#edit_payment_date').val(paid.payment_date || '');--}}
{{--                            } else {--}}
{{--                                console.warn('Payment method is null or undefined');--}}
{{--                                // Clear the input fields--}}
{{--                                $('#edit_amount_paid').val('');--}}
{{--                                $('#edit_payment_method').val('');--}}
{{--                                $('#edit_bank_name').val('');--}}
{{--                                $('#edit_card_number').val('');--}}
{{--                                $('#edit_payment_date').val('');--}}
{{--                            }--}}

{{--                        }--}}
{{--                        $('#editTicketModal').modal('show'); // Show the modal--}}
{{--                    }--}}
{{--                },--}}
{{--                error: function () {--}}
{{--                    alert('Failed to fetch ticket data.');--}}
{{--                }--}}
{{--            });--}}
{{--        }--}}

{{--        // Update Ticket Function (sends updated data to the backend)--}}
{{--        $('#editTicketForm').submit(function (e) {--}}
{{--            e.preventDefault();--}}

{{--            let ticketId = $('#edit_ticket_id').val();--}}
{{--            let formData = $(this).serialize();--}}

{{--            $.ajax({--}}
{{--                url: `/tickets/${ticketId}`, // Backend route to update ticket--}}
{{--                type: 'PUT',--}}
{{--                data: formData,--}}
{{--                headers: {--}}
{{--                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
{{--                },--}}
{{--                success: function (response) {--}}
{{--                    if (response.success) {--}}
{{--                        alert('Ticket updated successfully!');--}}
{{--                        $('#editTicketModal').modal('hide');--}}
{{--                        location.reload();--}}
{{--                    } else {--}}
{{--                        $.each(response.errors, function (key, value) {--}}
{{--                            $(`.edit_${key}_error`).text(value[0]);--}}
{{--                        });--}}
{{--                    }--}}
{{--                },--}}
{{--                error: function () {--}}
{{--                    console.error(xhr);--}}
{{--                    if (xhr.status === 422) {--}}
{{--                        $.each(xhr.responseJSON.errors, function (key, value) {--}}
{{--                            $('.' + key + '_error').text(value[0]);--}}
{{--                        });--}}
{{--                    }--}}
{{--                    alert('Failed to update ticket.');--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}

{{--        // Attach click event listener to edit buttons--}}
{{--        $(document).on('click', '.editTicketBtn', function () {--}}
{{--            let ticketId = $(this).data('id');--}}
{{--            editTicket(ticketId);--}}
{{--        });--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function () {--}}
{{--            // Attach event listener to all buttons with class 'open-payment-modal'--}}
{{--            const buttons = document.querySelectorAll('.open-payment-modal');--}}
{{--            buttons.forEach(button => {--}}
{{--                button.addEventListener('click', function () {--}}
{{--                    // Get data attributes from the button--}}
{{--                    const ticketId = this.getAttribute('data-ticket-id');--}}
{{--                    const ticketNumber = this.getAttribute('data-ticket-number');--}}
{{--                    const customerId = this.getAttribute('data-customer-id');--}}
{{--                    const pendingAmount = this.getAttribute('data-pending-amount');--}}

{{--                    // Set modal inputs--}}
{{--                    document.getElementById('modalTicketId').value = ticketId;--}}
{{--                    document.getElementById('modalCustomerId').value = customerId;--}}
{{--                    document.getElementById('modalTicketNumber').value = ticketNumber;--}}
{{--                    document.getElementById('modalPendingAmount').value = pendingAmount;--}}
{{--                });--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        $(document).on('click', '.open-refund-modal', function() {--}}
{{--            var ticketId = $(this).data('ticket-id');--}}
{{--            var saleId = $(this).data('sale-id');--}}
{{--            var salePrice = $(this).data('sale-price');--}}

{{--            // Populate the modal with the ticket details--}}
{{--            $('#ticketId').val(ticketId);--}}
{{--            $('#saleId').val(saleId);--}}
{{--            $('#refundAmount').val(salePrice);  // Refund amount is initially the sale price--}}
{{--            $('#amountToRefund').val(salePrice); // Amount to refund is initially the sale price--}}

{{--            // Optionally, you can calculate the amount to refund after subtracting service charges--}}
{{--            $('#serviceCharges').on('input', function() {--}}
{{--                var serviceCharges = parseFloat($(this).val()) || 0;--}}
{{--                var amountToRefund = salePrice - serviceCharges;--}}
{{--                $('#amountToRefund').val(amountToRefund.toFixed(2));--}}
{{--            });--}}
{{--        });--}}

{{--    </script>--}}
{{--    <script>--}}
{{--        function loadPaymentHistory(ticketId, page = 1) {--}}
{{--            $('#paymentHistoryContent').html('<p class="text-center">Loading...</p>');--}}

{{--            $.ajax({--}}
{{--                url: `/tickets/${ticketId}/payment-history`,--}}
{{--                method: 'GET',--}}
{{--                data: { page },--}}
{{--                success: function (response) {--}}
{{--                    if (response.success) {--}}
{{--                        // Update summary fields--}}
{{--                        $('#ticketNumber').text(response.ticket_number);--}}
{{--                        $('#totalPaid').text(`€${response.total_paid}`);--}}
{{--                        $('#pendingAmount').text(`€${response.total_pending}`);--}}

{{--                        // Populate table--}}
{{--                        const tableBody = $('#paymentHistoryTable');--}}
{{--                        tableBody.empty();--}}
{{--                        if (response.payments.length > 0) {--}}
{{--                            response.payments.forEach((payment, index) => {--}}
{{--                                tableBody.append(`--}}
{{--                            <tr>--}}
{{--                                <td>${index + 1}</td>--}}
{{--                                <td>€${payment.amount}</td>--}}
{{--                                <td>${payment.method}</td>--}}
{{--                                <td>${payment.notes}</td>--}}
{{--                                <td>${payment.date}</td>--}}
{{--                            </tr>--}}
{{--                        `);--}}
{{--                            });--}}

{{--                            // Generate pagination links--}}
{{--                            const totalPages = Math.ceil(response.pagination.total / response.pagination.per_page);--}}
{{--                            let paginationLinks = '';--}}
{{--                            for (let i = 1; i <= totalPages; i++) {--}}
{{--                                paginationLinks += `--}}
{{--                            <li class="page-item ${i === response.pagination.current_page ? 'active' : ''}">--}}
{{--                                <a class="page-link" href="#" onclick="loadPaymentHistory(${ticketId}, ${i}); return false;">${i}</a>--}}
{{--                            </li>`;--}}
{{--                            }--}}
{{--                            $('#paymentHistoryPagination').html(paginationLinks);--}}
{{--                        } else {--}}
{{--                            tableBody.html('<tr><td colspan="5" class="text-center">No payment history available.</td></tr>');--}}
{{--                            $('#paymentHistoryPagination').empty();--}}
{{--                        }--}}
{{--                    } else {--}}
{{--                        $('#paymentHistoryContent').html(`<p class="text-center text-danger">${response.message}</p>`);--}}
{{--                    }--}}
{{--                },--}}
{{--                error: function () {--}}
{{--                    $('#paymentHistoryContent').html('<p class="text-center text-danger">Failed to load payment history. Please try again later.</p>');--}}
{{--                }--}}
{{--            });--}}
{{--        }--}}

{{--        $(document).on('click', '.paymentHistoryTicketBtn', function () {--}}
{{--            const ticketId = $(this).data('id');--}}
{{--            loadPaymentHistory(ticketId);--}}
{{--        });--}}


{{--    </script>--}}

{{--@endsection--}}
