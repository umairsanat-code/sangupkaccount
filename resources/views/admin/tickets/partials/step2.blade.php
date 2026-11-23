<form id="step-form" action="{{ route('tickets.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="flight_date">Flight Date</label>
        <input type="date" name="flight_date" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="departure_city">Departure City</label>
        <input type="text" name="departure_city" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="destination_city">Destination City</label>
        <input type="text" name="destination_city" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Submit</button>
</form>
