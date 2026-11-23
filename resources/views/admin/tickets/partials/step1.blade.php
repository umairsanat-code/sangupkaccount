<form id="step-form" action="{{ route('tickets.step1.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="ticket_number">Ticket Number</label>
        <input type="text" name="ticket_number" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="airline_id">Airline</label>
        <select name="airline_id" class="form-control" required>
            <option value="">Select Airline</option>
            @foreach ($airlines as $airline)
                <option value="{{ $airline->id }}">{{ $airline->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="customer_id">Customer</label>
        <select name="customer_id" class="form-control" required>
            <option value="">Select Customer</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Next</button>
</form>
