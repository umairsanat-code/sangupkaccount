@extends('layouts.master')

@section('title', 'Agencies / Suppliers')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Agencies / Suppliers</h3>
                    <a href="{{ route('visa_umrah.agencies.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Agency</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="agencyTable">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th>City / Country</th>
                            <th>Services</th>
                            <th>Bookings</th>
                            <th>Total Payable</th>
                            <th>Total Paid</th>
                            <th>Outstanding</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($agencies as $a)
                            <tr>
                                <td><a href="{{ route('visa_umrah.agencies.show', $a->id) }}"><strong>{{ $a->name }}</strong></a></td>
                                <td>{{ $a->contact_person ?? '-' }}</td>
                                <td>{{ $a->phone ?? '-' }}</td>
                                <td>{{ $a->city ?? '-' }}, {{ $a->country ?? '-' }}</td>
                                <td>
                                    @foreach(($a->services ?? []) as $s)
                                        <span class="badge badge-info">{{ ucfirst($s) }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $a->booking_count }}</td>
                                <td class="text-right">{{ number_format($a->agency_payable, 2) }}</td>
                                <td class="text-right text-success">{{ number_format($a->agency_paid, 2) }}</td>
                                <td class="text-right {{ $a->outstanding > 0 ? 'text-danger' : '' }}">{{ number_format($a->outstanding, 2) }}</td>
                                <td>
                                    <a href="{{ route('visa_umrah.agencies.show', $a->id) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('visa_umrah.agencies.edit', $a->id) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('visa_umrah.agencies.destroy', $a->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete agency?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
            $('#agencyTable').DataTable({order: [[0, 'asc']]});
        });
    </script>
@endsection