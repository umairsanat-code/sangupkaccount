<?php $__env->startSection('title', 'Ticket Details'); ?>

<?php $__env->startSection('styles'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
                        <button onclick="generateInvoice(<?php echo e($customer->id); ?>)" class="btn btn-sm btn-secondary">
                            <i class="fas fa-file-invoice mr-1"></i> Generate Invoice
                        </button>
                        <button onclick="finalGenerateInvoice(<?php echo e($customer->id); ?>)" class="btn btn-sm btn-tertiary">
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
                                    <input type="text" class="form-control" readonly value="<?php echo e($customer->name); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Pax Email</label>
                                    <input type="text" class="form-control" readonly value="<?php echo e($customer->email); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Pax Reference</label>
                                    <input type="text" class="form-control" readonly value="<?php echo e($customer->pnr); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Service Number</label>
                                    <input type="text" class="form-control" readonly value="<?php echo e($customer->service_number); ?>">
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
                                        <span class="info-box-number">€<?php echo e(number_format($totalSalePrice,2)); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Issue Price</span>
                                        <span class="info-box-number">€<?php echo e(number_format($totalPurchasePrice,2)); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending Amount</span>
                                        <span class="info-box-number">€<?php echo e(number_format($totalPendingAmount,2)); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="info-box bg-secondary">
                                    <span class="info-box-icon"><i class="fas fa-undo"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Refund Amount</span>
                                        <span class="info-box-number">€<?php echo e(number_format($refundedAMount,2)); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="info-box <?php echo e($totalProfit>=0 ? 'bg-success' : 'bg-danger'); ?>">
                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Profit</span>
                                        <span class="info-box-number">€<?php echo e(number_format($totalProfit,2)); ?></span>
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
                                <span class="badge badge-primary"><?php echo e($tickets->total()); ?></span>
                            </a>
                            <a class="nav-item nav-link" id="refunded-tab" data-toggle="tab" href="#refundedTickets">Refunded Tickets
                                <span class="badge badge-secondary"><?php echo e($refundedTickets->total()); ?></span>
                            </a>
                        </div>
                    </nav>
                    <div class="tab-content p-3">
                        <!-- Active Tickets -->
                        <div class="tab-pane fade show active" id="activeTickets">
                            <div id="activeTicketsContainer">
                                <?php echo $__env->make('admin.tickets.partials.active_ajax', ['tickets'=>$tickets], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                            <ul class="pagination justify-content-center mt-3" id="activePagination"></ul>
                        </div>

                        <!-- Refunded Tickets -->
                        <div class="tab-pane fade" id="refundedTickets">
                            <div id="refundedTicketsContainer">
                                <?php echo $__env->make('admin.tickets.partials.refunded_ajax', ['refundedTickets'=>$refundedTickets], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                            <ul class="pagination justify-content-center mt-3" id="refundedPagination"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php echo $__env->make('admin.tickets.partials.add', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.tickets.partials.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.tickets.partials.payment_history', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="/payment-clear" method="POST">
                            <?php echo csrf_field(); ?>
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
                        <form action="<?php echo e(route('ticket.processRefund')); ?>" method="POST" id="refundForm">
                            <?php echo csrf_field(); ?>
                            <form action="<?php echo e(route('ticket.processRefund')); ?>" method="POST" id="refundForm">
                            <?php echo csrf_field(); ?>
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
                                    <?php $__errorArgs = ['airline_charges'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label for="airlineCharges">Airline Charges</label>
                                    <input type="number" id="airlineCharges" name="airline_charges" class="form-control" value="0" required>
                                    <?php $__errorArgs = ['airline_charges'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label for="amountToRefund">Amount to Refund</label>
                                    <input type="number" id="amountToRefund" name="amount_to_refund" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="refundReason">Refund Reason</label>
                                    <textarea id="refundReason" name="refund_reason" class="form-control" required></textarea>
                                    <?php $__errorArgs = ['refund_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        function generateInvoice(ticketId) {
                    window.open(`/tickets/${ticketId}/invoice`, '_blank');
                }
                function finalGenerateInvoice(ticketId) {
                    window.open(`/tickets/${ticketId}/final_invoice`, '_blank');
                }
        const customerId = <?php echo e($customer->id); ?>;

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
                                url: '<?php echo e(route("tickets.store")); ?>',
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
                        action: '<?php echo e(route("tickets.destroy", ":id")); ?>'.replace(':id', ticketId)
                    });

                    form.append($('<input>', { type: 'hidden', name: '_token', value: '<?php echo e(csrf_token()); ?>' }));
                    form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));

                    $('body').append(form);
                    form.submit();
                }
            });
        });
    </script>


<?php $__env->stopSection(); ?>



























































































































































































































































































































































































































































































































































































































































































































































































































































<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/tickets/ticket-detail.blade.php ENDPATH**/ ?>