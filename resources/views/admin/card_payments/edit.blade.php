@extends('layouts.master')

@section('title', 'Edit Card Payment')

@section('content')
    <div class="content-wrapper py-4">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Card Payment</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('card-payments.update', $cardPayment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.card_payments._form', ['submitLabel' => 'Update Payment'])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
