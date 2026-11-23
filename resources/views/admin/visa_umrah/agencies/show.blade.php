@extends('layouts.master')

@section('title', 'Agency: ' . $agency->name)

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('visa_umrah.agencies.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    <a href="{{ route('visa_umrah.agencies.edit', $agency->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                    <button class="btn btn-info btn-sm" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0">{{ $agency->name }}</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr><th style="width:200px">Contact Person</th><td>{{ $agency->contact_person ?? '-' }}</td></tr>
                                <tr><th>Phone</th><td>{{ $agency->phone ?? '-' }}</td></tr>
                                <tr><th>WhatsApp</th><td>{{ $agency->whatsapp ?? '-' }}</td></tr>
                                <tr><th>Email</th><td>{{ $agency->email ?? '-' }}</td></tr>
                                <tr><th>Location</th><td>{{ $agency->city ?? '-' }}, {{ $agency->country ?? '-' }}</td></tr>
                                <tr><th>Address</th><td>{{ $agency->address ?? '-' }}</td></tr>
                                <tr>
                                    <th>Services</th>
                                    <td>
                                        @foreach(($agency->services ?? []) as $s)
                                            <span class="badge badge-info">{{ ucfirst($s) }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><div class="border rounded p-2 text-center bg-white">
                            <small class="text-muted">Total Payable</small>
                            <h6>€{{ number_format($totalPayable, 2) }}</h6>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-2 text-center bg-white">
                            <small class="text-muted">Total Paid</small>
                            <h6 class="text-success">€{{ number_format($totalPaid, 2) }}</h6>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-2 text-center bg-white">
                            <small class="text-muted">Outstanding Balance</small>
                            <h6 class="text-danger">€{{ number_format($totalPayable - $totalPaid, 2) }}</h6>
                        </div></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Main Service Bookings (Visa / Umrah)</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm" id="agencyBookingsTable">
                                <thead>
                                <tr>
                                    <th>Booking #</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Travel Date</th>
                                    <th>Ref #</th>
                                    <th>Payable</th>
                                    <th>Paid</th>
                                    <th>Pending</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bookings as $b)
                                    <tr>
                                        <td>{{ $b->booking_no }}</td>
                                        <td>{{ ucfirst($b->booking_type) }}</td>
                                        <td>{{ $b->client_name }}</td>
                                        <td>{{ $b->travel_date ?? '-' }}</td>
                                        <td>{{ $b->main_agency_reference ?? '-' }}</td>
                                        <td class="text-right">{{ number_format((float)$b->main_agency_cost + (float)$b->services_agency_sum, 2) }}</td>
                                        <td class="text-right">{{ number_format((float)$b->agency_paid_sum, 2) }}</td>
                                        <td class="text-right text-danger">
                                            {{ number_format(((float)$b->main_agency_cost + (float)$b->services_agency_sum) - (float)$b->agency_paid_sum, 2) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('visa_umrah.bookings.show', ['type' => $b->booking_type, 'booking' => $b->id]) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Services (Hotel / Transport) via this Agency</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm" id="agencyServicesTable">
                                <thead>
                                <tr>
                                    <th>Booking</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Client Price</th>
                                    <th>Agency Cost</th>
                                    <th>Paid</th>
                                    <th>Pending</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($services as $s)
                                    <tr>
                                        <td>{{ $s->booking->booking_no ?? '-' }}</td>
                                        <td>{{ ucfirst($s->service_type) }}</td>
                                        <td>{{ $s->name }}</td>
                                        <td class="text-right">{{ number_format($s->client_price, 2) }}</td>
                                        <td class="text-right">{{ number_format($s->agency_cost, 2) }}</td>
                                        <td class="text-right">{{ number_format((float)$s->agency_paid_sum, 2) }}</td>
                                        <td class="text-right text-danger">{{ number_format($s->agency_cost - (float)$s->agency_paid_sum, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
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
            $('#agencyBookingsTable').DataTable({order: [[0, 'desc']]});
            $('#agencyServicesTable').DataTable({order: [[0, 'desc']]});
        });
    </script>
@endsection