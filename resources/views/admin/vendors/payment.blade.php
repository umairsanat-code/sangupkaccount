@extends('layouts.master')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <h3>Total Payment for Agent (ID: {{ $agentId }}) in December</h3>
            <p>Total Payment: {{ $totalPayment }} AED</p>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Payment Amount</th>
                    <th>Payment Method</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->ticket_id }}</td>
                        <td>{{ $vendor->payment_amount }}</td>
                        <td>{{ $vendor->payment_method }}</td>
                        <td>{{ $vendor->start_date }}</td>
                        <td>{{ $vendor->end_date }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
