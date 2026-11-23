<div class="modal fade" id="editTicketModal" tabindex="-1" role="dialog" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="editTicketModalLabel">Edit Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTicketForm">
                <div class="modal-body">
                    <meta name="csrf-token" content="{{ csrf_token() }}">

                    <!-- Hidden Ticket ID -->
                    <input type="hidden" id="edit_ticket_id" name="ticket_id">

                    <!-- Ticket Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ticket_number">Ticket Number</label>
                                <input type="text" id="edit_ticket_number" name="ticket_number" class="form-control">
                                <span class="text-danger error-text edit_ticket_number_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_flight_date">Flight Date</label>
                                <input type="date" id="edit_flight_date" name="flight_date" class="form-control">
                                <span class="text-danger error-text edit_flight_date_error"></span>
                            </div>
                        </div>
                    </div>
<input type="hidden" id="edit_sale_id" name="sale_id">
                    <!-- Airline and Booking Date -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_airline_id">Airline</label>
                                <select id="edit_airline_id" name="airline_id" class="form-control">
                                    <option value="">Select Airline</option>
                                    @php
                                        $airlines=\App\Models\Airline::get();
                                     @endphp
                                    @foreach ($airlines as $airline)
                                        <option value="{{ $airline->id }}"
                                                @if (old('airline_id') == $airline->id) selected @endif>
                                            {{ $airline->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text edit_airline_id_error"></span>
                            </div>
                        </div>
                           <div class="col-md-6">
                            <div class="form-group">
                                <label for="departure_city">Pax  Name</label>
                                 <input type="text" name="customer_name" id="edit_customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="">
                                 <span class="text-danger error-text customer_name_error"></span>
                            </div>

                        </div>

                    </div>

                    <!-- Departure and Destination City -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_departure_city">Departure City</label>
                                <input type="text" id="edit_departure_city" name="departure_city" class="form-control">
                                <span class="text-danger error-text edit_departure_city_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_destination_city">Destination City</label>
                                <input type="text" id="edit_destination_city" name="destination_city" class="form-control">
                                <span class="text-danger error-text edit_destination_city_error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Class and Status -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_class">Class</label>
                                <select id="edit_class" name="class" class="form-control">
                                    <option value="">Select Class</option>
                                    <option value="Economy">Economy</option>
                                    <option value="Business">Business</option>
                                    <option value="First Class">First Class</option>
                                </select>
                                <span class="text-danger error-text edit_class_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select id="edit_status" name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="Booked">Booked</option>
                                    <option value="Cancelled">Cancelled</option>
                                    <option value="VOIDED">VOIDED</option>
                                </select>
                                <span class="text-danger error-text edit_status_error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_purchase_price">Issue Price</label>
                                <input type="number" step="0.01" id="edit_purchase_price" name="purchase_price" class="form-control">
                                <span class="text-danger error-text edit_purchase_price_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_sale_price">Sale Price</label>
                                <input type="number" step="0.01" id="edit_sale_price" name="sale_price" class="form-control">
                                <span class="text-danger error-text edit_sale_price_error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Date -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="edit_sale_date">Issue Date</label>
                            <input type="date" id="edit_sale_date" name="sale_date" class="form-control">
                            <span class="text-danger error-text edit_sale_date_error"></span>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_payment_status">Payment Status</label>
                                <select id="edit_payment_status" name="payment_status" class="form-control">
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                </select>
                                <span class="text-danger error-text edit_payment_status_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="edit_sale_date">NIE </label>
                            <input type="text" id="edit_idCard" name="id_card" class="form-control">
                            <span class="text-danger error-text edit_sale_date_error"></span>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_payment_status">Passport No</label>

                                <input type="text" id="edit_passportno" name="passport_no" class="form-control">

                                <span class="text-danger error-text edit_payment_status_error"></span>
                            </div>
                        </div>
                    </div>
                      <div class="row">
                        <div class="col-md-6">
                            <label for="edit_sale_date">Pnr</label>
                            <input type="text" id="edit_pnr_date" name="pnr" class="form-control">
                            <span class="text-danger error-text edit_sale_date_error"></span>
                        </div>
                       @php
                              $agents=\App\Models\Agent::get();
                          @endphp
                          <div class="col-md-6">
                              <label for="edit_note">Vendor Name</label>
                              <select id="agent_id_select" name="agent_id" class="form-control">
                                  <option value="">Select Status</option>
                                  @foreach($agents as $agent)
                                      <option value="{{$agent->id}}">{{ $agent->name }}</option>

                                  @endforeach
                              </select>
                              <span class="text-danger error-text edit_note_error"></span>
                          </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="edit_note">Note</label>
                            <textarea rows="" cols="" class="form-control" name="note" id="edit_note"></textarea>
                            <span class="text-danger error-text edit_note_error"></span>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="form-group">
                        <input type="checkbox" id="edit_payment_checkbox" name="payment_received" value="1">
                        <label for="edit_payment_checkbox">Payment Received</label>
                    </div>
                    <div id="edit_payment_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_amount_paid">Amount Paid</label>
                                    <input type="number" step="0.01" id="edit_amount_paid" name="amount_paid" class="form-control">
                                    <span class="text-danger error-text edit_amount_paid_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_payment_method">Payment Method</label>
                                    <select id="edit_payment_method" name="payment_method" class="form-control">
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
                                    <label for="edit_bank_name">Bank Name</label>
                                    <input type="text" id="edit_bank_name" name="bank_name" class="form-control">
                                    <span class="text-danger error-text edit_bank_name_error"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_card_number">Card Number</label>
                                    <input type="text" id="edit_card_number" name="card_number" class="form-control">
                                    <span class="text-danger error-text edit_card_number_error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_payment_date">Payment Date</label>
                            <input type="date" id="edit_payment_date" name="payment_date" class="form-control">
                            <span class="text-danger error-text edit_payment_date_error"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Ticket</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{--<script>--}}
{{--    document.getElementById('edit_payment_checkbox').addEventListener('change', function () {--}}
{{--        const paymentFields = document.getElementById('edit_payment_fields');--}}
{{--        paymentFields.style.display = this.checked ? 'block' : 'none';--}}
{{--    });--}}


{{--</script>--}}
<script>
    // Simulating database status; replace this with the actual value from the database.
    const databasePaymentStatus = 'true'; // Example: Replace with `response.data.sales[0].payment_status`

    // Set the checkbox state based on the database value
    const paymentCheckbox = document.getElementById('edit_payment_checkbox');
    if (databasePaymentStatus === 'true') {
        paymentCheckbox.checked = true;
    } else if (databasePaymentStatus === 'false') {
        paymentCheckbox.checked = false;
    }

    // Trigger the change event to execute associated logic
    const paymentFields = document.getElementById('edit_payment_fields');
    paymentFields.style.display = paymentCheckbox.checked ? 'block' : 'none';

    // Add event listener for manual changes
    paymentCheckbox.addEventListener('change', function () {
        paymentFields.style.display = this.checked ? 'block' : 'none';
    });
</script>
