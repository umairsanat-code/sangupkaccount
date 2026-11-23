@extends('layouts.master')

@section('title', 'Visa & Umrah Reports')

@section('style')
    <style>
        .report-section { margin-bottom: 30px; }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between">
                    <h4 class="mb-0">Visa & Umrah Reports</h4>
                    <div>
                        <button class="btn btn-info btn-sm" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                        <a href="{{ route('visa_umrah.dashboard') }}" class="btn btn-secondary btn-sm"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </div>
                </div>
            </div>

            {{-- FILTERS --}}
            <div class="card mb-3">
                <div class="card-header bg-white"><h5 class="mb-0">Filters</h5></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('visa_umrah.reports') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filter['date_from'] }}">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filter['date_to'] }}">
                            </div>
                            <div class="col-md-2">
                                <label>Type</label>
                                <select name="booking_type" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    <option value="visa" {{ $filter['booking_type'] == 'visa' ? 'selected' : '' }}>Visa</option>
                                    <option value="umrah" {{ $filter['booking_type'] == 'umrah' ? 'selected' : '' }}>Umrah</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Visa Country</label>
                                <select name="visa_country" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c }}" {{ $filter['visa_country'] == $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Client</label>
                                <input type="text" name="client_name" class="form-control form-control-sm" value="{{ $filter['client_name'] }}">
                            </div>
                            <div class="col-md-2">
                                <label>Agency</label>
                                <select name="agency_id" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    @foreach($agencies as $a)
                                        <option value="{{ $a->id }}" {{ $filter['agency_id'] == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Status</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    @foreach(\App\Models\VisaUmrahBooking::VISA_STATUSES as $s)
                                        <option value="{{ $s }}" {{ $filter['status'] == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary btn-sm mr-1"><i class="fas fa-filter"></i> Apply</button>
                                <a href="{{ route('visa_umrah.reports') }}" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- OVERALL P&L --}}
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Overall Profit / Loss</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Total Sales</small><h6>€{{ number_format($overall['total_sales'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Client Received</small><h6 class="text-success">€{{ number_format($overall['client_received'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Client Pending</small><h6 class="text-danger">€{{ number_format($overall['client_pending'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Agency Payable</small><h6>€{{ number_format($overall['total_agency_payable'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Agency Paid</small><h6>€{{ number_format($overall['agency_paid'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Agency Pending</small><h6 class="text-danger">€{{ number_format($overall['agency_pending'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Expenses</small><h6>€{{ number_format($overall['expenses'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-success text-white mb-2">
                            <small>Gross Profit</small><h6>€{{ number_format($overall['gross_profit'], 2) }}</h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-primary text-white mb-2">
                            <small>Net Profit</small><h6>€{{ number_format($overall['net_profit'], 2) }}</h6>
                        </div></div>
                    </div>
                </div>
            </div>

            {{-- CLIENT LEDGER --}}
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Client Ledger</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="clientLedgerTable">
                        <thead>
                        <tr><th>Client</th><th>Phone</th><th>Bookings</th><th>Total Sale</th><th>Received</th><th>Pending</th></tr>
                        </thead>
                        <tbody>
                        @foreach($clientLedger as $row)
                            <tr>
                                <td>{{ $row['client_name'] }}</td>
                                <td>{{ $row['phone'] ?? '-' }}</td>
                                <td>{{ $row['bookings'] }}</td>
                                <td class="text-right">{{ number_format($row['total'], 2) }}</td>
                                <td class="text-right text-success">{{ number_format($row['received'], 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($row['pending'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- AGENCY LEDGER --}}
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Agency Ledger</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="agencyLedgerTable">
                        <thead>
                        <tr><th>Agency</th><th>Total Payable</th><th>Total Paid</th><th>Outstanding</th></tr>
                        </thead>
                        <tbody>
                        @foreach($agencyLedger as $row)
                            <tr>
                                <td>{{ $row['agency'] }}</td>
                                <td class="text-right">{{ number_format($row['payable'], 2) }}</td>
                                <td class="text-right text-success">{{ number_format($row['paid'], 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($row['pending'], 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- BOOKING PROFIT REPORT --}}
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Booking Profit Report (Visa / Umrah)</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="bookingProfitTable">
                        <thead>
                        <tr>
                            <th>Booking #</th><th>Type</th><th>Client</th><th>Travel Date</th>
                            <th>Client Sale</th><th>Client Received</th><th>Agency Payable</th><th>Agency Paid</th>
                            <th>Gross Profit</th><th>Net Profit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bookings as $b)
                            <tr>
                                <td>{{ $b->booking_no }}</td>
                                <td>{{ ucfirst($b->booking_type) }}</td>
                                <td>{{ $b->client_name }}</td>
                                <td>{{ $b->travel_date ?? '-' }}</td>
                                <td class="text-right">{{ number_format($b->client_total_full, 2) }}</td>
                                <td class="text-right text-success">{{ number_format((float)$b->cr_sum, 2) }}</td>
                                <td class="text-right">{{ number_format($b->agency_total_full, 2) }}</td>
                                <td class="text-right">{{ number_format((float)$b->ap_sum, 2) }}</td>
                                <td class="text-right">{{ number_format($b->gross_profit, 2) }}</td>
                                <td class="text-right {{ $b->net_profit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($b->net_profit, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- HOTEL PROFIT --}}
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Hotel Profit Report</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="hotelProfitTable">
                        <thead>
                        <tr><th>Hotel</th><th>City</th><th>Booking</th><th>Client Price</th><th>Agency Cost</th><th>Profit</th></tr>
                        </thead>
                        <tbody>
                        @php $hotelTotals = [0,0,0]; @endphp
                        @foreach($hotelServices as $h)
                            @php
                                $hotelTotals[0] += $h->client_price; $hotelTotals[1] += $h->agency_cost; $hotelTotals[2] += $h->profit();
                            @endphp
                            <tr>
                                <td>{{ $h->name }}</td>
                                <td>{{ $h->city }}</td>
                                <td>{{ $h->booking->booking_no ?? '-' }}</td>
                                <td class="text-right">{{ number_format($h->client_price, 2) }}</td>
                                <td class="text-right">{{ number_format($h->agency_cost, 2) }}</td>
                                <td class="text-right">{{ number_format($h->profit(), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-weight-bold">
                            <td>Total</td><td></td><td></td>
                            <td class="text-right">{{ number_format($hotelTotals[0], 2) }}</td>
                            <td class="text-right">{{ number_format($hotelTotals[1], 2) }}</td>
                            <td class="text-right">{{ number_format($hotelTotals[2], 2) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TRANSPORT PROFIT --}}
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Transport Profit Report</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="transportProfitTable">
                        <thead>
                        <tr><th>Route</th><th>Type</th><th>Booking</th><th>Client Price</th><th>Agency Cost</th><th>Profit</th></tr>
                        </thead>
                        <tbody>
                        @php $transportTotals = [0,0,0]; @endphp
                        @foreach($transportServices as $t)
                            @php
                                $transportTotals[0] += $t->client_price; $transportTotals[1] += $t->agency_cost; $transportTotals[2] += $t->profit();
                            @endphp
                            <tr>
                                <td>{{ $t->pickup_location }} → {{ $t->dropoff_location }}</td>
                                <td>{{ $t->vehicle_type }}</td>
                                <td>{{ $t->booking->booking_no ?? '-' }}</td>
                                <td class="text-right">{{ number_format($t->client_price, 2) }}</td>
                                <td class="text-right">{{ number_format($t->agency_cost, 2) }}</td>
                                <td class="text-right">{{ number_format($t->profit(), 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-weight-bold">
                            <td>Total</td><td></td><td></td>
                            <td class="text-right">{{ number_format($transportTotals[0], 2) }}</td>
                            <td class="text-right">{{ number_format($transportTotals[1], 2) }}</td>
                            <td class="text-right">{{ number_format($transportTotals[2], 2) }}</td>
                        </tr>
                        </tbody>
                    </table>
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
            ['clientLedgerTable', 'agencyLedgerTable', 'bookingProfitTable', 'hotelProfitTable', 'transportProfitTable'].forEach(function (id) {
                $('#' + id).DataTable();
            });
        });
    </script>
@endsection