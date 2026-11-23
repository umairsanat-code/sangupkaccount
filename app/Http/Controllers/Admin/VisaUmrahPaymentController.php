<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisaUmrahPaymentController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $booking = VisaUmrahBooking::findOrFail($bookingId);

        $data = $request->validate([
            'service_id' => 'nullable|exists:visa_umrah_services,id',
            'party' => 'required|in:client,agency',
            'payment_method' => 'required|in:cash,bank,card,online',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
            'reference' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $serviceId = $data['service_id'] ?: null;

        // Keep service_id pointing to the same booking only
        if ($serviceId) {
            $service = $booking->services()->find($serviceId);
            if (!$service) {
                return response()->json(['success' => false, 'message' => 'Invalid service.'], 422);
            }
        }

        VisaUmrahPayment::create(array_merge($data, [
            'booking_id' => $booking->id,
            'service_id' => $serviceId,
            'created_by' => Auth::id(),
        ]));

        return response()->json(['success' => true, 'message' => 'Payment recorded.']);
    }

    public function destroy($bookingId, $paymentId)
    {
        $payment = VisaUmrahPayment::where('booking_id', $bookingId)->findOrFail($paymentId);
        $payment->delete();

        return response()->json(['success' => true, 'message' => 'Payment deleted.']);
    }
}