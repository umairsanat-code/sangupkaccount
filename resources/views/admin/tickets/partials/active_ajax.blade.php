<div class="table-responsive">
    <table class="table table-striped custom-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Ticket Number</th>
            <th>Added By</th>
            <th>Pax Name</th>
            <th>Flight Date</th>
            <th>PNR</th>
            <th>Status</th>
            <th>Refund Status</th>
            <th>Issue Price</th>
            <th>Sale Price</th>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>
        @foreach($tickets as $ticket)
            @php
                $pendingAmount = $ticket->sales->sum('pending_amount');
            @endphp
            <tr class="{{ $pendingAmount > 0 ? 'table-warning' : '' }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ $ticket->author_name }}</td>
                <td>{{ $ticket->customer_name??$ticket->customer->name }}</td>
                <td>{{ \Carbon\Carbon::parse($ticket->flight_date)->format('d-m-Y') }}</td>
                <td>{{ $ticket->pnr }}</td>
                <td>{{ $ticket->status }}</td>
                <td>{{ $ticket->refund_status }}</td>
                <td>€{{ $ticket->purchase_price }}</td>
                <td>€{{ $ticket->sales->sum('sale_price') }}</td>
                    <td>
                    @if ($pendingAmount > 0)
                        <button class="btn btn-outline-success btn-sm open-payment-modal"
                                data-ticket-id="{{ $ticket->id }}"
                                data-ticket-number="{{ $ticket->ticket_number }}"
                                data-customer-id="{{ $customer->id }}"
                                data-pending-amount="{{ $pendingAmount }}"
                                data-toggle="modal"
                                data-target="#paymentModal"
                                title="Make Payment">
                            <i class="fas fa-euro-sign"></i>
                        </button>
                    @else
                        <span class="btn btn-outline-success btn-sm" title="Cleared">
                            <i class="fas fa-check-circle"></i>
                        </span>
                    @endif

                    <!-- Refund Button -->
                    <button class="btn btn-outline-danger btn-sm open-refund-modal"
                            data-ticket-id="{{ $ticket->id }}"
                            data-sale-id="{{ $ticket->sales()->first()->id ?? '' }}"
                            data-sale-price="{{ $ticket->sales()->first()->sale_price ?? 0 }}"
                            data-toggle="modal"
                            data-target="#refundModal"
                            title="Refund">
                        <i class="fas fa-undo"></i>
                    </button>
                @if(auth()->id()=='1'|| auth()->id()=='2')
                    <!-- Edit Button -->
                    <button class="btn btn-outline-primary btn-sm editTicketBtn"
                            data-id="{{ $ticket->id }}"
                            title="Edit Ticket">
                        <i class="fas fa-edit"></i>
                    </button>
                    @endif
                    <!-- Payment History Button -->
                    <button class="btn btn-outline-info btn-sm paymentHistoryTicketBtn"
                            data-id="{{ $ticket->id }}"
                            data-toggle="modal"
                            data-target="#paymentHistoryModal"
                            title="Payment History">
                        <i class="fas fa-history"></i>
                    </button>
                    @if(auth()->id()=='1')
                    <!-- Delete Button -->
                                <button class="btn btn-outline-danger btn-sm deleteTicketBtn"
                                        data-id="{{ $ticket->id }}"
                                        title="Delete Ticket">
                                    <i class="fas fa-trash"></i>
                                </button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
