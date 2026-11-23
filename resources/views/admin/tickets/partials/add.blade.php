<!-- Modal Trigger Button -->


<!-- Modal -->
<div class="modal fade" id="ticketFormModal" tabindex="-1" aria-labelledby="ticketFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketFormModalLabel">Create Ticket</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="ticketForm">
                    <meta name="csrf-token" content="{{ csrf_token() }}">

                    <!-- Hidden Customer ID Field -->
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                    <!-- Ticket Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ticket_number">Ticket Number</label>
                                <input type="text" name="ticket_number" class="form-control @error('ticket_number') is-invalid @enderror" value="{{ old('ticket_number') }}" >
                                <span class="text-danger error-text ticket_number_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="flight_date">Flight Date</label>
                                <input type="date" name="flight_date" class="form-control @error('flight_date') is-invalid @enderror" value="{{ old('flight_date') }}" required>
                                <span class="text-danger error-text flight_date_error"></span>

                            </div>
                        </div>
                    </div>

                    <!-- Airline and Booking Date -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="airline_id">Airline</label>
                                <select name="airline_id" class="form-control @error('airline_id') is-invalid @enderror" required>
                                    <option value="">Select Airline</option>
                                    @foreach ($airlines as $airline)
                                        <option value="{{ $airline->id }}" {{ old('airline_id') == $airline->id ? 'selected' : '' }}>{{ $airline->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text flight_date_error"></span>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
{{--                                @php--}}
{{--                                $customer_name=\App\Models\Customer::with('Tickets')->where('id',$customer->id)->get();--}}
{{--                                @endphp--}}
{{--                                @if($customer_name->count()!=0)--}}
{{--                                    <label for="departure_city">Pax  Name</label>--}}
{{--                                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}">--}}
{{--                                    <span class="text-danger error-text customer_name_error"></span>--}}
{{--                                @else--}}
{{--                                    <label for="departure_city">Pax  Name</label>--}}
{{--                                    <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ $customer->name }}">--}}
{{--                                    <span class="text-danger error-text customer_name_error"></span>--}}
{{--                                @endif--}}
                                @php
                                    $hasTickets = \App\Models\Ticket::where('customer_id', $customer->id)->exists();
                                @endphp

                                <label for="departure_city">Pax Name</label>
                                <input type="text" name="customer_name"
                                       class="form-control @error('customer_name') is-invalid @enderror"
                                       value="{{ old('customer_name', $hasTickets ? '' : $customer->name) }}">
                                <span class="text-danger error-text customer_name_error"></span>

                            </div>
                            <!--<div class="form-group">-->
                            <!--    <label for="booking_date">Booking Date</label>-->
                            <!--    <input type="date" name="booking_date" class="form-control @error('booking_date') is-invalid @enderror" value="{{ old('booking_date') }}" required>-->
                            <!--    <span class="text-danger error-text booking_date_error"></span>-->


                            <!--</div>-->
                        </div>
                    </div>

                    <!-- Departure and Destination City -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departure_city">Departure City</label>
                                <input type="text" name="departure_city" class="form-control @error('departure_city') is-invalid @enderror" value="{{ old('departure_city') }}" required>
                                <span class="text-danger error-text departure_city_error"></span>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="destination_city">Destination City</label>
                                <input type="text" name="destination_city" class="form-control @error('destination_city') is-invalid @enderror" value="{{ old('destination_city') }}" required>
                                <span class="text-danger error-text destination_city_error"></span>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departure_city">Vendor  Name</label>
                               <select class="form-control"  id="agent_id" name="agent_id">
                                   <option> Select Vendor Name</option>
                                   @foreach($agents as $agent)
                                       <option value="{{$agent->id}}">{{$agent->name}}</option>
                                   @endforeach
                               </select>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                @php
                                    $hasTickets = \App\Models\Ticket::where('customer_name', $customer->name)->exists();
//                                    dd($hasTickets,$customer->customer_pnr);
                                @endphp

                                <label for="destination_city">PNR</label>
                                <input type="text" name="pnr"
                                       class="form-control @error('pnr') is-invalid @enderror"
                                       value="{{ old('pnr', $hasTickets ?   '':$customer->customer_pnr) }}"
                                       placeholder="Enter Pnr" required>
                                <span class="text-danger error-text pnr_error"></span>

                            </div>
                        </div>

                    </div>

                    <!-- Class and Status -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class">Class</label>
                                <select name="class" class="form-control @error('class') is-invalid @enderror" required>
                                    <option value="">Select Class</option>
                                    <option value="Economy" {{ old('class') == 'Economy' ? 'selected' : '' }}>Economy</option>
                                    <option value="Business" {{ old('class') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="First Class" {{ old('class') == 'First Class' ? 'selected' : '' }}>First Class</option>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                 <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="">Select Status</option>
                                    <option value="Booked" {{ old('status') == 'Booked' ? 'selected' : '' }}>Issue Ticket</option>
                                    <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Re-Issue Ticket</option>
                                            <option value="VOIDED" {{ old('status') == 'VOIDED' ? 'selected' : '' }}>VOIDED Ticket</option>

                                </select>
                                @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purchase_price">Issue Price</label>
                                <input type="text"  name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price') }}" required>
                                <span class="text-danger error-text purchase_price_error"></span>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sale_price">Sale Price</label>
                                <input type="text"  name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price') }}" required>
                                <span class="text-danger error-text sale_price_error"></span>


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="sale_date">Issue Date</label>
                            <input type="date" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror" value="{{ old('sale_date') }}" required>
                            <span class="text-danger error-text sale_date_error"></span>

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
                    <div class="row">
                        <div class="col-md-6">
                            <label for="sale_date">NIE</label>
                            <input type="text" name="id_card" class="form-control @error('id_card') is-invalid @enderror" value="{{ old('id_card') }}" required>
                            <span class="text-danger error-text id_card_error"></span>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_status">Passport Number</label>
                                <input type="text" name="passport_no" class="form-control @error('passport_no') is-invalid @enderror" value="{{ old('passport_no') }}" required>

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

                    <!-- Payment Details -->
                    <div class="form-group">
                        <input type="checkbox" id="payment_checkbox" name="payment_received" value="1">
                        <label for="payment_checkbox">Payment Received</label>
                    </div>
                    <div id="payment_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount_paid">Amount Paid</label>
                                    <input type="number" step="0.01" name="amount_paid" class="form-control" value="{{ old('amount_paid') }}">
                                    <span class="text-danger error-text amount_paid_error"></span>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Bank">Bank</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_name">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}">
                                    <span class="text-danger error-text bank_name_error"></span>


                                </div>
                            </div>
<div class="col-md-6">
                            <label for="payment_date">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date') }}">
                            <span class="text-danger error-text payment_date_error"></span>

                        </div>
                        </div>


                    </div>

                    <button type="submit" id="ticketFormBtn" class="btn btn-primary">Create Ticket</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('payment_checkbox').addEventListener('change', function () {
        const paymentFields = document.getElementById('payment_fields');
        paymentFields.style.display = this.checked ? 'block' : 'none';
    });


</script>

