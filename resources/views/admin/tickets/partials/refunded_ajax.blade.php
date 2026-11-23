<div class="table-responsive">
    <table class="table table-striped custom-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Ticket Number</th>
            <th>Flight Date</th>
            <th>Status</th>
            <th>Refund Status</th>
            <th>Issue Price</th>
            <th>Refund Amount</th>
            <th>Refund Charges</th>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>
        @foreach($refundedTickets as $ticket)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ $ticket->flight_date }}</td>
                <td>{{ $ticket->status }}</td>
                <td>{{ $ticket->refund_status }}</td>
                <td>€{{ $ticket->sales->sum('sale_price') }}</td>
                <td>€{{ $ticket->sales->sum('refund_amount') }}</td>
                <td>€{{ $ticket->sales->sum('service_charges') + $ticket->sales->sum('airline_charges') }}</td>
                <td>
                    <button
                        class="btn btn-success btn-sm"
                        onclick="activateRefundedTicket({{ $ticket->id }})">
                        Activate Again
                    </button>
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function activateRefundedTicket(ticketId) {

        Swal.fire({
            title: 'Activate Ticket?',
            text: 'This refunded ticket will be activated again.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Activate',
            cancelButtonText: 'Cancel'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: '/tickets/' + ticketId + '/activate-refund',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Activated!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Reload refunded list
                        loadRefundedTickets();
                    },
                    error: function () {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            }
        });
    }
</script>

