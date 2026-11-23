@extends('layouts.master')
@section('title', 'Create Ticket')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Create Ticket</h1>
                    </div>
                </div>
            </div>
        </section>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-header" style="background-color: white;">
                                <h3 class="card-title">Add New Ticket</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('tickets.store') }}" method="POST">
                                    @csrf

                                    <div class="row">

                                        <!-- Customer and Flight Date fields in one row -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_id">Customer</label>
                                                <select id="customer_id" name="customer_id" class="form-control select2 @error('customer_id') is-invalid @enderror" required>
                                                    <option value="">Select Customer</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" data-total-customer="{{ $customer->total_customer }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('customer_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="airline_id">Airline</label>
                                                <select name="airline_id" class="form-control @error('airline_id') is-invalid @enderror" required>
                                                    <option value="">Select Airline</option>
                                                    @foreach ($airlines as $airline)
                                                        <option value="{{ $airline->id }}" {{ old('airline_id') == $airline->id ? 'selected' : '' }}>{{ $airline->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('airline_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Ticket Number and Airline fields in one row -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="ticket_number">Ticket Number</label>
                                                <input type="text" name="ticket_number" class="form-control @error('ticket_number') is-invalid @enderror" value="{{ old('ticket_number') }}" required>
                                                @error('ticket_number')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="flight_date">Flight Date</label>
                                                <input type="date" name="flight_date" class="form-control @error('flight_date') is-invalid @enderror" value="{{ old('flight_date') }}" required>
                                                @error('flight_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Booking Date and Departure City fields in one row -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="booking_date">Booking Date</label>
                                                <input type="date" name="booking_date" class="form-control @error('booking_date') is-invalid @enderror" value="{{ old('booking_date') }}" required>
                                                @error('booking_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="departure_city">Departure City</label>
                                                <input type="text" name="departure_city" class="form-control @error('departure_city') is-invalid @enderror" value="{{ old('departure_city') }}" required>
                                                @error('departure_city')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Destination City and Class fields in one row -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="destination_city">Destination City</label>
                                                <input type="text" name="destination_city" class="form-control @error('destination_city') is-invalid @enderror" value="{{ old('destination_city') }}" required>
                                                @error('destination_city')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="class">Class</label>
                                                <select name="class" class="form-control @error('class') is-invalid @enderror" required>
                                                    <option value="">Select Class</option>
                                                    <option value="Economy" {{ old('class') == 'Economy' ? 'selected' : '' }}>Economy</option>
                                                    <option value="Business" {{ old('class') == 'Business' ? 'selected' : '' }}>Business</option>
                                                    <option value="First Class" {{ old('class') == 'First Class' ? 'selected' : '' }}>First Class</option>
                                                </select>
                                                @error('class')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Status and Purchase Price fields in one row -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                                    <option value="">Select Status</option>
                                                    <option value="Booked" {{ old('status') == 'Booked' ? 'selected' : '' }}>Booked</option>
                                                    <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    <option value="Checked In" {{ old('status') == 'Checked In' ? 'selected' : '' }}>Checked In</option>
                                                </select>
                                                @error('status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="purchase_price">Purchase Price</label>
                                                <input type="number" step="0.01" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price') }}" required>
                                                @error('purchase_price')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- Status and Purchase Price fields in one row -->
                                        <div class="col-md-6">

                                            <div class="form-group">
                                                <label for="sale_price">Sale Price</label>
                                                <input type="number" step="0.01" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price') }}" required>
                                                @error('sale_price')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>


                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sale_date">Sale Date</label>
                                                <input type="date" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror" value="{{ old('sale_date') }}" required>
                                                @error('sale_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>


                                        </div>
                                    </div>
                                    <div class="row">
                                        <!-- Status and Purchase Price fields in one row -->
                                        <div class="col-md-6">

                                            <div class="form-group">
{{--                                                <label for="customer">Total Customer</label>--}}
{{--                                                <input type="text" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror" value="{{$customer->total_customer}}" required>--}}
                                                <label for="total_customer">Total Customer</label>
                                                <input type="text" id="total_customer" name="total_ticket_customer" class="form-control @error('total_ticket_customer') is-invalid @enderror" value="{{ old('total_ticket_customer') }}" required>

                                                @error('agent_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>




                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="payment_status">Payment Status</label>
                                                <select name="payment_status" class="form-control @error('payment_status') is-invalid @enderror" required>
                                                    <option value="Paid" {{ old('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                                    <option value="Pending" {{ old('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                </select>
                                                @error('payment_status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>


                                        </div>
                                    </div>

                           <div class="form-group">
                            <label for="payment_date">Note</label>
                            <textarea rows="" cols="" class="form-control" name="note" id="note"></textarea>
                            <span class="text-danger error-text note_error"></span>

                              </div>



                                    <div class="form-group">
                                        <input type="checkbox" id="payment_checkbox" name="payment_received" value="1">
                                        <label for="payment_checkbox">Payment Received</label>
                                    </div>

                                    <!-- Payment Details Fields -->
                                    <div id="payment_fields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="amount_paid">Amount Paid</label>
                                                    <input type="number" step="0.01" name="amount_paid" class="form-control @error('amount_paid') is-invalid @enderror" value="{{ old('amount_paid') }}">
                                                    @error('amount_paid')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="payment_method">Payment Method</label>
                                                    <select name="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                                                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                        <option value="Card" {{ old('payment_method') == 'Card' ? 'selected' : '' }}>Card</option>
                                                        <option value="Bank" {{ old('payment_method') == 'Bank' ? 'selected' : '' }}>Bank</option>
                                                    </select>
                                                    @error('payment_method')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bank_name">Bank Name</label>
                                                    <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}">
                                                    @error('bank_name')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <label for="payment_date">Payment Date</label>
                                            <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date') }}">
                                            @error('payment_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>



                                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

            <script>
                document.getElementById('payment_checkbox').addEventListener('change', function () {
                    const paymentFields = document.getElementById('payment_fields');
                    if (this.checked) {
                        paymentFields.style.display = 'block';
                    } else {
                        paymentFields.style.display = 'none';
                    }
                });
            </script>


            <script type="text/javascript">
                $(document).ready(function() {
                    // Initialize Select2 on the customer select dropdown
                    $('#customer_id').select2();

                    // When a customer is selected, update the total customer field
                    $('#customer_id').on('change', function() {
                        const selectedOption = $(this).find('option:selected');
                        const totalCustomer = selectedOption.data('total-customer');

                        // Set the value of the "Total Customer" field
                        $('#total_customer').val(totalCustomer);
                    });

                    // Trigger the change event if a customer was already selected on page load
                    const initialCustomer = $('#customer_id').val();
                    if (initialCustomer) {
                        const initialOption = $('#customer_id').find('option:selected');
                        const initialTotalCustomer = initialOption.data('total-customer');
                        $('#total_customer').val(initialTotalCustomer);
                    }
                });


            </script>
@endsection

