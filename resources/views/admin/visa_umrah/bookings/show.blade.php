@extends('layouts.master')

@section('title', 'Booking ' . $booking->booking_no)

@section('style')
    <style>
        .badge-status {
            font-size: 0.8rem;
            padding: 6px 10px;
        }
        .financial-summary .row { align-items: center; }
        .nav-tabs .nav-link { font-weight: 600; }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="{{ route('visa_umrah.bookings.index', ['type' => $type]) }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    <a href="{{ route('visa_umrah.bookings.edit', ['type' => $type, 'booking' => $booking->id]) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                    <a href="{{ route('visa_umrah.bookings.print', ['type' => $type, 'booking' => $booking->id]) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-print"></i> Print</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                {{ ucfirst($type) }} Booking <strong>#{{ $booking->booking_no }}</strong>
                                @php
                                    $badgeMap = ['pending'=>'warning','documents_required'=>'info','processing'=>'primary','approved'=>'success','rejected'=>'danger','completed'=>'dark','cancelled'=>'secondary','new'=>'secondary','confirmed'=>'primary','active'=>'success'];
                                    if ($type === 'visa') {
                                        $color = $badgeMap[$booking->visa_status] ?? 'secondary';
                                        $label = $booking->visa_status;
                                    } else {
                                        $color = $badgeMap[$booking->booking_status] ?? 'secondary';
                                        $label = $booking->booking_status;
                                    }
                                @endphp
                                <span class="badge badge-{{ $color }} badge-status">{{ ucwords(str_replace('_', ' ', $label ?? '')) }}</span>
                            </h3>
                            <span class="text-muted">Created: {{ $booking->created_at->format('d M Y H:i') }}</span>
                        </div>

                        <div class="card-body">
                            <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-overview">Overview</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-services">Hotel & Transport</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-payments">Payments</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-documents">Documents</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-expenses">Expenses</a></li>
                            </ul>

                            <div class="tab-content pt-3">
                                {{-- OVERVIEW --}}
                                <div class="tab-pane fade show active" id="tab-overview">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Client Information</h6>
                                            <table class="table table-sm table-striped">
                                                <tr><th style="width:200px">Client Name</th><td>{{ $booking->client_name }}</td></tr>
                                                <tr><th>Phone / WhatsApp</th><td>{{ $booking->client_phone ?? '-' }}</td></tr>
                                                <tr><th>Email</th><td>{{ $booking->client_email ?? '-' }}</td></tr>
                                                <tr><th>Passport Number</th><td>{{ $booking->passport_number ?? '-' }}</td></tr>
                                                <tr><th>Nationality</th><td>{{ $booking->nationality ?? '-' }}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">{{ ucfirst($type) }} Details</h6>
                                            <table class="table table-sm table-striped">
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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Agency / Supplier</h6>
                                            <table class="table table-sm table-striped">
                                                <tr><th style="width:200px">Agency</th><td>{{ $booking->mainAgency->name ?? '-' }}</td></tr>
                                                <tr><th>Reference</th><td>{{ $booking->main_agency_reference ?? '-' }}</td></tr>
                                                <tr><th>Agency Cost (€)</th><td>{{ number_format($booking->main_agency_cost, 2) }}</td></tr>
                                                <tr><th>Client Total (€)</th><td>{{ number_format($booking->main_client_total, 2) }}</td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Notes</h6>
                                            <div class="p-2 bg-light border rounded">{{ $booking->notes ?: '-' }}</div>
                                        </div>
                                    </div>

                                    {{-- FINANCIAL SUMMARY --}}
                                    @php
                                        $clientTotal = $booking->clientTotalFull();
                                        $clientReceived = $booking->clientReceivedTotal();
                                        $clientPending = $clientTotal - $clientReceived;
                                        $agencyTotal = $booking->agencyTotalFull();
                                        $agencyPaid = $booking->agencyPaidTotal();
                                        $agencyPending = $agencyTotal - $agencyPaid;
                                        $gross = $booking->grossProfit();
                                        $expensesTotal = $booking->additionalExpensesTotal();
                                        $net = $booking->netProfit();
                                        $receivedProfit = $booking->receivedProfit();
                                    @endphp
                                    <hr>
                                    <h6 class="text-success">Financial Summary (EUR €)</h6>
                                    <div class="row">
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-light">
                                            <small class="text-muted">Total Client Sale</small>
                                            <h6 class="mb-0">{{ number_format($clientTotal, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-light">
                                            <small class="text-muted">Client Received</small>
                                            <h6 class="mb-0 text-success">{{ number_format($clientReceived, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-light">
                                            <small class="text-muted">Client Pending</small>
                                            <h6 class="mb-0 text-danger">{{ number_format($clientPending, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-light">
                                            <small class="text-muted">Total Agency Payable</small>
                                            <h6 class="mb-0">{{ number_format($agencyTotal, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-light">
                                            <small class="text-muted">Agency Paid</small>
                                            <h6 class="mb-0 text-success">{{ number_format($agencyPaid, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-light">
                                            <small class="text-muted">Agency Pending</small>
                                            <h6 class="mb-0 text-danger">{{ number_format($agencyPending, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-light">
                                            <small class="text-muted">Expenses</small>
                                            <h6 class="mb-0 text-warning">{{ number_format($expensesTotal, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-primary text-white">
                                            <small>Gross Profit</small>
                                            <h6 class="mb-0">{{ number_format($gross, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-success text-white">
                                            <small>Net Profit</small>
                                            <h6 class="mb-0">{{ number_format($net, 2) }}</h6>
                                        </div></div>
                                        <div class="col-md-3 col-6"><div class="border rounded p-2 text-center mb-2 bg-info text-white">
                                            <small>Received Profit</small>
                                            <h6 class="mb-0">{{ number_format($receivedProfit, 2) }}</h6>
                                        </div></div>
                                    </div>
                                </div>

                                {{-- SERVICES --}}
                                <div class="tab-pane fade" id="tab-services">
                                    <button class="btn btn-primary btn-sm mb-2 add-service-btn" data-type="hotel"><i class="fas fa-hotel"></i> Add Hotel</button>
                                    <button class="btn btn-primary btn-sm mb-2 add-service-btn" data-type="transport"><i class="fas fa-shuttle-van"></i> Add Transport</button>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="servicesTable">
                                            <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Name / Route</th>
                                                <th>Dates</th>
                                                <th>Details</th>
                                                <th>Agency</th>
                                                <th>Agency Cost</th>
                                                <th>Client Price</th>
                                                <th>Profit</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($booking->services as $svc)
                                                <tr>
                                                    <td><span class="badge badge-info">{{ ucfirst($svc->service_type) }}</span></td>
                                                    <td>{{ $svc->name }}</td>
                                                    <td>
                                                        @if($svc->isHotel())
                                                            {{ $svc->check_in }} → {{ $svc->check_out }}
                                                            ({{ $svc->num_nights }} nights)
                                                        @else
                                                            {{ $svc->pickup_date }} {{ $svc->pickup_time }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($svc->isHotel())
                                                            {{ $svc->room_type }} ({{ $svc->num_rooms }} rooms, {{ $svc->num_guests }} guests) • {{ ucwords(str_replace('_',' ',$svc->meal_plan)) }}
                                                        @else
                                                            {{ $svc->pickup_location }} → {{ $svc->dropoff_location }} • {{ $svc->vehicle_type }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $svc->agency->name ?? '-' }}
                                                        <div class="text-muted small">
                                                            Paid: {{ number_format($svc->agencyPaidTotal(), 2) }} |
                                                            Received: {{ number_format($svc->clientReceivedTotal(), 2) }}
                                                        </div>
                                                    </td>
                                                    <td class="text-right">{{ number_format($svc->agency_cost, 2) }}</td>
                                                    <td class="text-right">{{ number_format($svc->client_price, 2) }}</td>
                                                    <td class="text-right">{{ number_format($svc->profit(), 2) }}</td>
                                                    <td>
                                                        <button class="btn btn-xs btn-danger delete-service" data-id="{{ $svc->id }}"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- PAYMENTS --}}
                                <div class="tab-pane fade" id="tab-payments">
                                    <button class="btn btn-success btn-sm mb-2 add-payment-btn"><i class="fas fa-money-bill-wave"></i> Add Payment</button>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="paymentsTable">
                                            <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Party</th>
                                                <th>Service</th>
                                                <th>Method</th>
                                                <th>Amount</th>
                                                <th>Reference</th>
                                                <th>Remarks</th>
                                                <th>Added By</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($booking->payments as $pay)
                                                <tr>
                                                    <td>{{ $pay->payment_date ?? $pay->created_at->format('Y-m-d') }}</td>
                                                    <td><span class="badge badge-{{ $pay->party === 'client' ? 'success' : 'danger' }}">{{ ucfirst($pay->party) }}</span></td>
                                                    <td>
                                                        @if($pay->service)
                                                            {{ ucfirst($pay->service->service_type) }}: {{ $pay->service->name }}
                                                        @else
                                                            Main {{ ucfirst($booking->booking_type) }}
                                                        @endif
                                                    </td>
                                                    <td>{{ ucfirst($pay->payment_method) }}</td>
                                                    <td class="text-right">{{ number_format($pay->amount, 2) }}</td>
                                                    <td>{{ $pay->reference ?? '-' }}</td>
                                                    <td>{{ $pay->remarks ?? '-' }}</td>
                                                    <td>{{ $pay->user->firstName ?? '-' }}</td>
                                                    <td><button class="btn btn-xs btn-danger delete-payment" data-id="{{ $pay->id }}"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- DOCUMENTS --}}
                                <div class="tab-pane fade" id="tab-documents">
                                    <form id="documentForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Label</label>
                                                <input type="text" name="label" class="form-control" placeholder="e.g. Passport Scan">
                                            </div>
                                            <div class="col-md-6">
                                                <label>File (jpg, png, pdf, doc)</label>
                                                <input type="file" name="document" class="form-control" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button class="btn btn-primary btn-sm" id="uploadDocumentBtn"><i class="fas fa-upload"></i> Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                    <hr>
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                        <tr><th>Label</th><th>File</th><th>Uploaded</th><th>Actions</th></tr>
                                        </thead>
                                        <tbody id="documentsBody">
                                        @foreach($booking->documents as $doc)
                                            <tr>
                                                <td>{{ $doc->label ?? '-' }}</td>
                                                <td><a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-download"></i> Download</a></td>
                                                <td>{{ $doc->created_at->format('d M Y H:i') }}</td>
                                                <td><button class="btn btn-xs btn-danger delete-document" data-id="{{ $doc->id }}"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- EXPENSES --}}
                                <div class="tab-pane fade" id="tab-expenses">
                                    <form id="expenseForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Description</label>
                                                <input type="text" name="description" class="form-control" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Amount (€)</label>
                                                <input type="number" name="amount" step="0.01" min="0" class="form-control" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Date</label>
                                                <input type="date" name="expense_date" class="form-control">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add</button>
                                            </div>
                                        </div>
                                    </form>
                                    <hr>
                                    <small class="text-muted">Additional expenses deducted from Gross Profit to compute Net Profit.</small>
                                    <table class="table table-bordered table-sm mt-2">
                                        <thead>
                                        <tr><th>Description</th><th>Amount</th><th>Date</th><th>Actions</th></tr>
                                        </thead>
                                        <tbody id="expensesBody">
                                        @foreach($booking->expenses as $exp)
                                            <tr>
                                                <td>{{ $exp->description }}</td>
                                                <td class="text-right">{{ number_format($exp->amount, 2) }}</td>
                                                <td>{{ $exp->expense_date ?? '-' }}</td>
                                                <td><button class="btn btn-xs btn-danger delete-expense" data-id="{{ $exp->id }}"><i class="fas fa-trash"></i></button></td>
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
        </div>
    </div>

    {{-- SERVICE MODAL --}}
    <div class="modal fade" id="serviceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="serviceForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="serviceModalTitle">Add Service</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="service_type" id="service_type">
                        <div id="hotelFields">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Hotel Name *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Country</label>
                                    <input type="text" name="country" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Check-in Date</label>
                                    <input type="date" name="check_in" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Check-out Date</label>
                                    <input type="date" name="check_out" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label>Nights</label>
                                    <input type="number" name="num_nights" class="form-control" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label>Rooms</label>
                                    <input type="number" name="num_rooms" class="form-control" min="1">
                                </div>
                                <div class="col-md-2">
                                    <label>Guests</label>
                                    <input type="number" name="num_guests" class="form-control" min="1">
                                </div>
                                <div class="col-md-3">
                                    <label>Room Type</label>
                                    <select name="room_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="standard">Standard</option>
                                        <option value="deluxe">Deluxe</option>
                                        <option value="suite">Suite</option>
                                        <option value="twin">Twin</option>
                                        <option value="double">Double</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Meal Plan</label>
                                    <select name="meal_plan" class="form-control">
                                        <option value="">Select</option>
                                        <option value="room_only">Room Only</option>
                                        <option value="breakfast">Breakfast</option>
                                        <option value="half_board">Half Board</option>
                                        <option value="full_board">Full Board</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="transportFields" style="display:none">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Pickup Location</label>
                                    <input type="text" name="pickup_location" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Drop-off Location</label>
                                    <input type="text" name="dropoff_location" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Pickup Date</label>
                                    <input type="date" name="pickup_date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Pickup Time</label>
                                    <input type="time" name="pickup_time" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>Vehicle Type</label>
                                    <select name="vehicle_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="sedan">Sedan</option>
                                        <option value="van">Van</option>
                                        <option value="bus">Bus</option>
                                        <option value="minibus">Minibus</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Passengers</label>
                                    <input type="number" name="num_passengers" class="form-control" min="1">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="text-primary">Agency & Pricing</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Agency / Supplier</label>
                                <select name="agency_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($agencies as $a)
                                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Agency Reference</label>
                                <input type="text" name="agency_reference" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label>Agency Cost (€)</label>
                                <input type="number" name="agency_cost" step="0.01" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label>Client Price (€)</label>
                                <input type="number" name="client_price" step="0.01" min="0" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary btn-sm" type="submit">Add Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="paymentForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Payment</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Party</label>
                                <select name="party" class="form-control" required>
                                    <option value="client">Client (Received)</option>
                                    <option value="agency">Agency (Paid)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                    <option value="card">Card</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Amount (€)</label>
                                <input type="number" name="amount" step="0.01" min="0.01" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Payment Date</label>
                                <input type="date" name="payment_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Service</label>
                                <select name="service_id" class="form-control">
                                    <option value="">Main {{ ucfirst($type) }}</option>
                                    @foreach($booking->services as $svc)
                                        <option value="{{ $svc->id }}">{{ ucfirst($svc->service_type) }}: {{ $svc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Reference</label>
                                <input type="text" name="reference" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label>Remarks</label>
                                <input type="text" name="remarks" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-success btn-sm" type="submit">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        var bookingId = {{ $booking->id }};
        var serviceStoreUrl = "{{ route('visa_umrah.bookings.services.store', ['booking' => $booking->id]) }}";
        var paymentStoreUrl = "{{ route('visa_umrah.bookings.payments.store', ['booking' => $booking->id]) }}";
        var documentStoreUrl = "{{ route('visa_umrah.bookings.documents.store', ['booking' => $booking->id]) }}";
        var expenseStoreUrl = "{{ route('visa_umrah.bookings.expenses.store', ['booking' => $booking->id]) }}";

        // Service modal
        $('.add-service-btn').on('click', function () {
            var t = $(this).data('type');
            $('#service_type').val(t);
            $('#serviceModalTitle').text(t === 'hotel' ? 'Add Hotel Booking' : 'Add Transport Booking');
            $('#hotelFields').toggle(t === 'hotel');
            $('#transportFields').toggle(t !== 'hotel');
            $('#serviceForm')[0].reset();
            $('#service_type').val(t);
            $('#serviceModal').modal('show');
        });

        $('#serviceForm').on('submit', function (e) {
            e.preventDefault();
            var type = $('#service_type').val();
            if ((type === 'hotel' && !$('input[name="name"]').val()) ||
                (type === 'transport' && !$('input[name="pickup_location"]').val() && !$('input[name="dropoff_location"]').val())) {
                toastr.error('Please fill required fields.');
                return;
            }
            $.ajax({
                url: serviceStoreUrl,
                method: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    toastr.success(res.message);
                    $('#serviceModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    var msg = 'Server error.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                    toastr.error(msg);
                }
            });
        });

        $(document).on('click', '.delete-service', function () {
            if (!confirm('Delete this service?')) return;
            var id = $(this).data('id');
            $.ajax({
                url: "{{ url('visa-umrah/bookings') }}/" + bookingId + "/services/" + id,
                method: 'DELETE',
                data: {_token: "{{ csrf_token() }}"},
                success: function () { toastr.success('Deleted'); location.reload(); }
            });
        });

        // Payment
        $('.add-payment-btn').on('click', function () {
            $('#paymentForm')[0].reset();
            $('#paymentModal').modal('show');
        });

        $('#paymentForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: paymentStoreUrl,
                method: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    toastr.success(res.message);
                    $('#paymentModal').modal('hide');
                    location.reload();
                },
                error: function (xhr) {
                    var msg = 'Server error.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors)[0][0];
                    toastr.error(msg);
                }
            });
        });

        $(document).on('click', '.delete-payment', function () {
            if (!confirm('Delete this payment?')) return;
            var id = $(this).data('id');
            $.ajax({
                url: "{{ url('visa-umrah/bookings') }}/" + bookingId + "/payments/" + id,
                method: 'DELETE',
                data: {_token: "{{ csrf_token() }}"},
                success: function () { toastr.success('Deleted'); location.reload(); }
            });
        });

        // Documents
        $('#documentForm').on('submit', function (e) {
            e.preventDefault();
            var fd = new FormData(this);
            $.ajax({
                url: documentStoreUrl,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function (res) {
                    toastr.success(res.message);
                    location.reload();
                },
                error: function () { toastr.error('Upload failed.'); }
            });
        });

        $(document).on('click', '.delete-document', function () {
            if (!confirm('Delete this document?')) return;
            var id = $(this).data('id');
            $.ajax({
                url: "{{ url('visa-umrah/bookings') }}/" + bookingId + "/documents/" + id,
                method: 'DELETE',
                data: {_token: "{{ csrf_token() }}"},
                success: function () { toastr.success('Deleted'); location.reload(); }
            });
        });

        // Expenses
        $('#expenseForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: expenseStoreUrl,
                method: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    toastr.success(res.message);
                    location.reload();
                },
                error: function () { toastr.error('Error adding expense.'); }
            });
        });

        $(document).on('click', '.delete-expense', function () {
            if (!confirm('Delete this expense?')) return;
            var id = $(this).data('id');
            $.ajax({
                url: "{{ url('visa-umrah/bookings') }}/" + bookingId + "/expenses/" + id,
                method: 'DELETE',
                data: {_token: "{{ csrf_token() }}"},
                success: function () { toastr.success('Deleted'); location.reload(); }
            });
        });
    </script>
@endsection