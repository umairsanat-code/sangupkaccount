<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardPayment;
use App\Models\User;
use Illuminate\Http\Request;

class CardPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = CardPayment::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('bank_name')) {
            $query->where('bank_name', $request->input('bank_name'));
        }

        if ($request->filled('received_date_from')) {
            $query->whereDate('received_date', '>=', $request->date('received_date_from'));
        }

        if ($request->filled('received_date_to')) {
            $query->whereDate('received_date', '<=', $request->date('received_date_to'));
        }

        $cardPayments = $query->paginate(15)->withQueryString();
        $users = User::query()->orderBy('firstName')->get(['id', 'firstName', 'lastName']);

        return view('admin.card_payments.index', [
            'cardPayments' => $cardPayments,
            'users' => $users,
            'bankNames' => CardPayment::BANK_NAMES,
        ]);
    }

    public function create()
    {
        return view('admin.card_payments.create', [
            'cardPayment' => new CardPayment(),
            'users' => User::query()->orderBy('firstName')->get(['id', 'firstName', 'lastName']),
            'bankNames' => CardPayment::BANK_NAMES,
        ]);
    }

    public function store(Request $request)
    {
        CardPayment::create($this->validateData($request));

        return redirect()
            ->route('card-payments.index')
            ->with('success', 'Card payment created successfully.');
    }

    public function edit(CardPayment $cardPayment)
    {
        return view('admin.card_payments.edit', [
            'cardPayment' => $cardPayment,
            'users' => User::query()->orderBy('firstName')->get(['id', 'firstName', 'lastName']),
            'bankNames' => CardPayment::BANK_NAMES,
        ]);
    }

    public function update(Request $request, CardPayment $cardPayment)
    {
        $cardPayment->update($this->validateData($request));

        return redirect()
            ->route('card-payments.index')
            ->with('success', 'Card payment updated successfully.');
    }

    public function destroy(CardPayment $cardPayment)
    {
        $cardPayment->delete();

        return redirect()
            ->route('card-payments.index')
            ->with('success', 'Card payment deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'bank_name' => ['nullable', 'in:' . implode(',', CardPayment::BANK_NAMES)],
            'card_payment' => ['nullable', 'string', 'max:50'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'received_date' => ['nullable', 'date'],
            'sent_amount' => ['nullable', 'numeric', 'min:0'],
            'sent_date' => ['nullable', 'date'],
        ]);
    }
}
