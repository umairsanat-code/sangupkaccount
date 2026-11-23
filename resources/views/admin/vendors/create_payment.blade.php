@extends('layouts.master')

@section('content')
    <div class="content-wrapper">

        <div class="max-w-3xl mx-auto my-10 p-6 bg-gradient-orange rounded-lg shadow-md">
            <h3 class="text-2xl font-semibold text-gray-700 mb-6">
                @if(isset($payment))
                    Edit Payment for Agent
                @else
                    Process Payment for Agent
                @endif
            </h3>

            <!-- Payment Form -->
            <form action="{{ isset($payment) ? route('vendors.payment.update', $payment->id) : route('vendors.payment.process') }}"
                  method="POST"
                  class="space-y-6">
                @csrf
                @if(isset($payment))
                    @method('PUT')
                @endif

                <div class="flex flex-col">
                    <label for="agent_id" class="text-sm font-medium text-gray-600 mb-2">Select Agent</label>
                    <select name="agent_id" id="agent_id" class="form-select bg-gray-100 border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select Agent</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}" {{ isset($payment) && $payment->agent_id == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-6">
                    <div class="flex-1">
                        <label for="start_date" class="text-sm font-medium text-gray-600 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3 w-full focus:ring-blue-500 focus:border-blue-500"
                               value="{{ $payment->start_date ?? '' }}" required>
                    </div>

                    <div class="flex-1">
                        <label for="end_date" class="text-sm font-medium text-gray-600 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3 w-full focus:ring-blue-500 focus:border-blue-500"
                               value="{{ $payment->end_date ?? '' }}" required>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label for="total_amount" class="text-sm font-medium text-gray-600 mb-2">Total Balance </label>
                    <input type="text" id="total_amount" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3"
                           value="{{ $payment->total_amount ?? '' }}" readonly>
                </div>

                <div class="flex flex-col">
                    <label for="remaining_amount" class="text-sm font-medium text-gray-600 mb-2">Remaining Balance</label>
                    <input type="text" id="remaining_amount" name="remaining_amount" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3"
                           value="{{ $payment->remaining_amount ?? '' }}" readonly>
                </div>

                <div class="flex flex-col">
                    <label for="ticket_count" class="text-sm font-medium text-gray-600 mb-2">Total Tickets</label>
                    <input type="text" id="ticket_count" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3"
                           value="{{ $payment->ticket_count ?? '' }}" readonly>
                </div>

                <div class="flex flex-col">
                    <label for="payment_amount" class="text-sm font-medium text-gray-600 mb-2">Deposit Amount</label>
                    <input type="text" name="payment_amount" id="payment_amount" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3"
                           value="{{ $payment->payment_amount ?? '' }}" required>
                </div>

                <div class="flex flex-col">
                    <label for="payment_method" class="text-sm font-medium text-gray-600 mb-2">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select bg-gray-100 border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="bank" {{ isset($payment) && $payment->payment_method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ isset($payment) && $payment->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ isset($payment) && $payment->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="payment_date" class="text-sm font-medium text-gray-600 mb-2">Payment Date</label>
                    <input type="date" name="payment_date" id="payment_date" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3 w-full focus:ring-blue-500 focus:border-blue-500"
                           value="{{ $payment->payment_date ?? '' }}" required>
                </div>

                <div class="flex flex-col mt-4" id="bank_names_wrapper">
                    <label for="bank_name" class="text-sm font-medium text-gray-600 mb-2">Bank Name</label>
                    <select name="bank_name" id="bank_name" class="form-input bg-gray-100 border-gray-300 rounded-lg p-3 w-full focus:ring-blue-500 focus:border-blue-500">
                        <option value="Sabadell" {{ isset($payment) && $payment->bank_name == 'Sabadell' ? 'selected' : '' }}>Sabadell</option>
                        <option value="BBVA" {{ isset($payment) && $payment->bank_name == 'BBVA' ? 'selected' : '' }}>BBVA</option>
                        <option value="Santander" {{ isset($payment) && $payment->bank_name == 'Santander' ? 'selected' : '' }}>Santander</option>
                        <option value="La Caixa" {{ isset($payment) && $payment->bank_name == 'La Caixa' ? 'selected' : '' }}>La Caixa</option>
                        <option value="Cajamar" {{ isset($payment) && $payment->bank_name == 'Cajamar' ? 'selected' : '' }}>Cajamar</option>
                        <option value="Ibercaja" {{ isset($payment) && $payment->bank_name == 'Ibercaja' ? 'selected' : '' }}>Ibercaja</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="note" class="text-sm font-medium text-gray-600 mb-2">Note</label>
                    <textarea
                        name="note"
                        id="note"
                        rows="10"
                        cols="40"
                        class="form-input bg-gray-100 border-gray-300 rounded-lg p-3 w-full focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your notes here..."
                        required
                    >{{ $payment->note ?? '' }}</textarea>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 active:from-blue-700 active:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if(isset($payment))
                            Update Payment
                        @else
                            Submit Payment
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


@section('script')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    function fetchAgentPurchaseAmount() {
    const agentId = $('#agent_id').val();
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();

    if (agentId && startDate && endDate) {
        $.ajax({
            url: '{{ route("get.total.purchase.amount") }}',
            method: 'POST',
            data: {
                agent_id: agentId,
                start_date: startDate,
                end_date: endDate,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                $('#total_amount').val(response.total_purchase_amount);
                $('#remaining_amount').val(response.remaining_amount);
                $('#ticket_count').val(response.ticket_count);
            },
            error: function () {
                alert('An error occurred while fetching agent details.');
            }
        });
    }
}

// ✅ jab bhi koi field change ho
$('#agent_id, #start_date, #end_date').on('change', fetchAgentPurchaseAmount);

// ✅ page load hote hi bhi ek dafa call ho
$(document).ready(function() {
    fetchAgentPurchaseAmount();
});

        // Ajax to fetch total purchase amount when the agent, start date, and end date are selected
        // $('#agent_id, #start_date, #end_date').on('change', function () {
        //     const agentId = $('#agent_id').val();
        //     const startDate = $('#start_date').val();
        //     const endDate = $('#end_date').val();

        //     if (agentId && startDate && endDate) {
        //         $.ajax({
        //             url: '{{ route("get.total.purchase.amount") }}',
        //             method: 'POST',
        //             data: {
        //                 agent_id: agentId,
        //                 start_date: startDate,
        //                 end_date: endDate,
        //                 _token: '{{ csrf_token() }}'
        //             },
        //             success: function (response) {
        //                 $('#total_amount').val(response.total_purchase_amount);
        //                 $('#remaining_amount').val(response.remaining_amount);
        //                 $('#ticket_count').val(response.ticket_count);
        //             },
        //             error: function () {
        //                 alert('An error occurred while fetching agent details.');
        //             }
        //         });
        //     }
        // });
    </script>
@endsection
