@extends('layouts.master')

@section('title', 'Card Payments')

@section('content')
    <div class="content-wrapper py-4">
        <div class="container-fluid">
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Card Payments</h3>
                    <a href="{{ route('card-payments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add Card Payment
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('card-payments.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">User</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                                            {{ trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? '')) ?: ('User #' . $user->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="bank_name" class="form-label">Bank</label>
                                <select name="bank_name" id="bank_name" class="form-control">
                                    <option value="">All Banks</option>
                                    @foreach($bankNames as $bankName)
                                        <option value="{{ $bankName }}" {{ request('bank_name') === $bankName ? 'selected' : '' }}>
                                            {{ $bankName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="received_date_from" class="form-label">Received From</label>
                                <input type="date" name="received_date_from" id="received_date_from" class="form-control" value="{{ request('received_date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="received_date_to" class="form-label">Received To</label>
                                <input type="date" name="received_date_to" id="received_date_to" class="form-control" value="{{ request('received_date_to') }}">
                            </div>

                            <div class="col-md-2 d-flex">
                                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                                <a href="{{ route('card-payments.index') }}" class="btn btn-outline-secondary w-100 ml-2">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Bank</th>
                                <th>Card/Ref</th>
                                <th>Received</th>
                                <th>Received Date</th>
                                <th>Sent</th>
                                <th>Sent Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cardPayments as $cardPayment)
                                <tr>
                                    <td>{{ $cardPayment->id }}</td>
                                    <td>{{ optional($cardPayment->user)->name ?? '-' }}</td>
                                    <td>{{ $cardPayment->bank_name ?: '-' }}</td>
                                    <td>{{ $cardPayment->card_payment ?: '-' }}</td>
                                    <td>{{ number_format((float) $cardPayment->received_amount, 2) }}</td>
                                    <td>{{ optional($cardPayment->received_date)->format('Y-m-d') ?: '-' }}</td>
                                    <td>{{ number_format((float) $cardPayment->sent_amount, 2) }}</td>
                                    <td>{{ optional($cardPayment->sent_date)->format('Y-m-d') ?: '-' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('card-payments.edit', $cardPayment) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                        <form action="{{ route('card-payments.destroy', $cardPayment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this card payment?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">No card payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    {{ $cardPayments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
