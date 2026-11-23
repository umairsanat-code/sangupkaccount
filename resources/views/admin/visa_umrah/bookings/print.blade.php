<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Booking {{ $booking->booking_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2937; font-size: 13px; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .summary-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; background: #f8fafc; }
        h5 { color: #2563eb; }
        table.striped td, table.striped th { padding: 5px 8px; }
        .profit-positive { color: #16a34a; font-weight: bold; }
        .profit-negative { color: #dc2626; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
<div class="container">
    <button class="btn btn-primary no-print mb-3" onclick="window.print()">Print</button>
    <div class="header">
        <h2 class="mb-0">SanguTravel</h2>
        <small>{{ ucfirst($type) }} Booking Confirmation</small>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <table class="table table-sm striped">
                <tr><th style="width:200px">Booking No</th><td><strong>{{ $booking->booking_no }}</strong></td></tr>
                <tr><th>Client Name</th><td>{{ $booking->client_name }}</td></tr>
                <tr><th>Phone</th><td>{{ $booking->client_phone ?? '-' }}</td></tr>
                <tr><th>Email</th><td>{{ $booking->client_email ?? '-' }}</td></tr>
                <tr><th>Passport</th><td>{{ $booking->passport_number ?? '-' }}</td></tr>
                <tr><th>Nationality</th><td>{{ $booking->nationality ?? '-' }}</td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm striped">
                @if($type === 'visa')
                    <tr><th style="width:200px">Visa Country</th><td>{{ $booking->visa_country ?? '-' }}</td></tr>
                    <tr><th>Visa Type</th><td>{{ $booking->visa_type ?? '-' }}</td></tr>
                    <tr><th>Travelers</th><td>{{ $booking->num_travelers ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ ucwords(str_replace('_', ' ', $booking->visa_status ?? '')) }}</td></tr>
                @else
                    <tr><th style="width:200px">Package</th><td>{{ $booking->umrah_package ?? '-' }}</td></tr>
                    <tr><th>Adults</th><td>{{ $booking->num_adults ?? '-' }}</td></tr>
                    <tr><th>Children</th><td>{{ $booking->num_children ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ ucwords($booking->booking_status ?? '-') }}</td></tr>
                @endif
                <tr><th>Travel Date</th><td>{{ $booking->travel_date ?? '-' }}</td></tr>
                <tr><th>Return Date</th><td>{{ $booking->return_date ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

    <h5>Services</h5>
    <table class="table table-bordered table-sm">
        <thead>
        <tr>
            <th>Service</th><th>Details</th><th>Agency</th>
            <th>Client Price</th><th>Agency Cost</th><th>Profit</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Main {{ ucfirst($type) }}</td>
            <td>{{ $booking->mainAgency->name ?? '-' }} (Ref: {{ $booking->main_agency_reference ?? '-' }})</td>
            <td>{{ $booking->mainAgency->name ?? '-' }}</td>
            <td>{{ number_format($booking->main_client_total, 2) }}</td>
            <td>{{ number_format($booking->main_agency_cost, 2) }}</td>
            <td class="profit-{{ ($booking->main_client_total - $booking->main_agency_cost) >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($booking->main_client_total - $booking->main_agency_cost, 2) }}
            </td>
        </tr>
        @foreach($booking->services as $svc)
            <tr>
                <td>{{ ucfirst($svc->service_type) }}: {{ $svc->name }}</td>
                <td>
                    @if($svc->isHotel())
                        {{ $svc->check_in }} → {{ $svc->check_out }} ({{ $svc->num_nights ?? 0 }}n) • {{ $svc->room_type }} • {{ ucwords(str_replace('_', ' ', $svc->meal_plan)) }}
                    @else
                        {{ $svc->pickup_location }} → {{ $svc->dropoff_location }} • {{ $svc->vehicle_type }}
                    @endif
                </td>
                <td>{{ $svc->agency->name ?? '-' }}</td>
                <td>{{ number_format($svc->client_price, 2) }}</td>
                <td>{{ number_format($svc->agency_cost, 2) }}</td>
                <td class="profit-{{ $svc->profit() >= 0 ? 'positive' : 'negative' }}">{{ number_format($svc->profit(), 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @php
        $clientTotal = $booking->clientTotalFull();
        $clientReceived = $booking->clientReceivedTotal();
        $agencyTotal = $booking->agencyTotalFull();
        $agencyPaid = $booking->agencyPaidTotal();
        $gross = $booking->grossProfit();
        $exp = $booking->additionalExpensesTotal();
        $net = $booking->netProfit();
        $recvProfit = $booking->receivedProfit();
    @endphp
    <h5>Financial Summary (EUR €)</h5>
    <div class="row">
        <div class="col-md-7">
            <table class="table table-sm striped">
                <tr><th>Total Client Sale</th><td>{{ number_format($clientTotal, 2) }}</td></tr>
                <tr><th>Client Received</th><td>{{ number_format($clientReceived, 2) }}</td></tr>
                <tr><th>Client Pending</th><td>{{ number_format($clientTotal - $clientReceived, 2) }}</td></tr>
                <tr><th>Total Agency Payable</th><td>{{ number_format($agencyTotal, 2) }}</td></tr>
                <tr><th>Agency Paid</th><td>{{ number_format($agencyPaid, 2) }}</td></tr>
                <tr><th>Agency Pending</th><td>{{ number_format($agencyTotal - $agencyPaid, 2) }}</td></tr>
            </table>
        </div>
        <div class="col-md-5">
            <div class="summary-box">
                <h6>Profit</h6>
                <p class="mb-1"><strong>Gross Profit:</strong> €{{ number_format($gross, 2) }}</p>
                <p class="mb-1"><strong>Expenses:</strong> €{{ number_format($exp, 2) }}</p>
                <p class="mb-0"><strong>Net Profit:</strong> €{{ number_format($net, 2) }}</p>
                <p class="mb-0 profit-positive"><strong>Received Profit:</strong> €{{ number_format($recvProfit, 2) }}</p>
            </div>
        </div>
    </div>

    @if($booking->notes)
        <h5>Notes</h5>
        <p>{{ $booking->notes }}</p>
    @endif
</div>
</body>
</html>