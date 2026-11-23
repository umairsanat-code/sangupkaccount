<div class="row g-3">
    <div class="col-md-6">
        <label for="user_id" class="form-label">User</label>
        <select name="user_id" id="user_id" class="form-control">
            <option value="">Select User</option>
            @foreach($users as $user)
                <option
                    value="{{ $user->id }}"
                    {{ old('user_id', $cardPayment->user_id) == $user->id ? 'selected' : '' }}
                >
                    {{ trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? '')) ?: ('User #' . $user->id) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="bank_name" class="form-label">Bank Name</label>
        <select name="bank_name" id="bank_name" class="form-control">
            <option value="">Select Bank</option>
            @foreach($bankNames as $bankName)
                <option
                    value="{{ $bankName }}"
                    {{ old('bank_name', $cardPayment->bank_name) === $bankName ? 'selected' : '' }}
                >
                    {{ $bankName }}
                </option>
            @endforeach
        </select>
    </div>

{{--    <div class="col-md-6">--}}
{{--        <label for="card_payment" class="form-label">Card Number / Reference</label>--}}
{{--        <input--}}
{{--            type="text"--}}
{{--            name="card_payment"--}}
{{--            id="card_payment"--}}
{{--            class="form-control"--}}
{{--            maxlength="50"--}}
{{--            value="{{ old('card_payment', $cardPayment->card_payment) }}"--}}
{{--            placeholder="Card number or reference"--}}
{{--        >--}}
{{--    </div>--}}

    <div class="col-md-3">
        <label for="received_amount" class="form-label">Received Amount</label>
        <input
            type="number"
            name="received_amount"
            id="received_amount"
            class="form-control"
            step="0.01"
            min="0"
            value="{{ old('received_amount', $cardPayment->received_amount) }}"
        >
    </div>

    <div class="col-md-3">
        <label for="received_date" class="form-label">Received Date</label>
        <input
            type="date"
            name="received_date"
            id="received_date"
            class="form-control"
            value="{{ old('received_date', optional($cardPayment->received_date)->format('Y-m-d')) }}"
        >
    </div>

    <div class="col-md-3">
        <label for="sent_amount" class="form-label">Sent Amount</label>
        <input
            type="number"
            name="sent_amount"
            id="sent_amount"
            class="form-control"
            step="0.01"
            min="0"
            value="{{ old('sent_amount', $cardPayment->sent_amount) }}"
        >
    </div>

    <div class="col-md-3">
        <label for="sent_date" class="form-label">Sent Date</label>
        <input
            type="date"
            name="sent_date"
            id="sent_date"
            class="form-control"
            value="{{ old('sent_date', optional($cardPayment->sent_date)->format('Y-m-d')) }}"
        >
    </div>
</div>

<div class="mt-4 d-flex">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> {{ $submitLabel ?? 'Save' }}
    </button>
    <a href="{{ route('card-payments.index') }}" class="btn btn-outline-secondary ml-2">Cancel</a>
</div>
