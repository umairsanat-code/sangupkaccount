<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisaUmrahExpenseController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $booking = VisaUmrahBooking::findOrFail($bookingId);

        $data = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
        ]);

        VisaUmrahExpense::create(array_merge($data, [
            'booking_id' => $booking->id,
            'created_by' => Auth::id(),
        ]));

        return response()->json(['success' => true, 'message' => 'Expense added.']);
    }

    public function destroy($bookingId, $expenseId)
    {
        $expense = VisaUmrahExpense::where('booking_id', $bookingId)->findOrFail($expenseId);
        $expense->delete();

        return response()->json(['success' => true, 'message' => 'Expense deleted.']);
    }
}