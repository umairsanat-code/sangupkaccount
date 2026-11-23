@extends('layouts.master')

@section('title', 'New ' . ucfirst($type) . ' Booking')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0">New {{ ucfirst($type) }} Booking</h3>
                            <a href="{{ route('visa_umrah.bookings.index', ['type' => $type]) }}" class="btn btn-secondary btn-sm float-right">Back</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('visa_umrah.bookings.store', ['type' => $type]) }}" method="POST">
                                @csrf

                                <h5 class="text-primary">Client Information</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Client Name *</label>
                                        <input type="text" name="client_name" class="form-control" required value="{{ old('client_name') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Phone / WhatsApp</label>
                                        <input type="text" name="client_phone" class="form-control" value="{{ old('client_phone') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Email</label>
                                        <input type="email" name="client_email" class="form-control" value="{{ old('client_email') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Passport Number</label>
                                        <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nationality</label>
                                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality') }}">
                                    </div>
                                </div>

                                <hr>
                                <h5 class="text-primary">{{ ucfirst($type) }} Details</h5>
                                <div class="row">
                                    @if($type === 'visa')
                                        <div class="col-md-4">
                                            <label>Visa Country</label>
                                            <input type="text" name="visa_country" class="form-control" value="{{ old('visa_country') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Visa Type</label>
                                            <input type="text" name="visa_type" class="form-control" placeholder="e.g. Tourist, Business" value="{{ old('visa_type') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Number of Travelers</label>
                                            <input type="number" name="num_travelers" class="form-control" min="1" value="{{ old('num_travelers') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Visa Status</label>
                                            <select name="visa_status" class="form-control">
                                                @foreach(\App\Models\VisaUmrahBooking::VISA_STATUSES as $s)
                                                    <option value="{{ $s }}" {{ old('visa_status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <label>Number of Adults</label>
                                            <input type="number" name="num_adults" class="form-control" min="1" value="{{ old('num_adults') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Number of Children</label>
                                            <input type="number" name="num_children" class="form-control" min="0" value="{{ old('num_children') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Umrah Package</label>
                                            <input type="text" name="umrah_package" class="form-control" placeholder="e.g. 5 Nights Makkah + 4 Nights Madinah" value="{{ old('umrah_package') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Booking Status</label>
                                            <select name="booking_status" class="form-control">
                                                @foreach(\App\Models\VisaUmrahBooking::BOOKING_STATUSES as $s)
                                                    <option value="{{ $s }}" {{ old('booking_status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="col-md-4">
                                        <label>Travel / Expected Arrival Date</label>
                                        <input type="date" name="travel_date" class="form-control" value="{{ old('travel_date') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Return / Departure Date</label>
                                        <input type="date" name="return_date" class="form-control" value="{{ old('return_date') }}">
                                    </div>
                                </div>

                                <hr>
                                <h5 class="text-primary">Agency / Supplier (Main Service)</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Agency / Supplier</label>
                                        <select name="main_agency_id" class="form-control">
                                            <option value="">Select Agency</option>
                                            @foreach($agencies as $a)
                                                <option value="{{ $a->id }}" {{ old('main_agency_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Agency Reference Number</label>
                                        <input type="text" name="main_agency_reference" class="form-control" value="{{ old('main_agency_reference') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Agency Cost (€)</label>
                                        <input type="number" name="main_agency_cost" step="0.01" min="0" class="form-control" value="{{ old('main_agency_cost', 0) }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Client Total (€)</label>
                                        <input type="number" name="main_client_total" step="0.01" min="0" class="form-control" value="{{ old('main_client_total', 0) }}">
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-primary">Create Booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection