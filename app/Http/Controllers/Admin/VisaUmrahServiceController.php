<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahService;
use Illuminate\Http\Request;

class VisaUmrahServiceController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $booking = VisaUmrahBooking::findOrFail($bookingId);

        $data = $request->validate([
            'service_type' => 'required|in:hotel,transport',
            'name' => 'required|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'num_nights' => 'nullable|integer|min:0',
            'room_type' => 'nullable|string',
            'num_rooms' => 'nullable|integer|min:1',
            'num_guests' => 'nullable|integer|min:1',
            'meal_plan' => 'nullable|string',
            'pickup_location' => 'nullable|string',
            'dropoff_location' => 'nullable|string',
            'pickup_date' => 'nullable|date',
            'pickup_time' => 'nullable',
            'vehicle_type' => 'nullable|string',
            'num_passengers' => 'nullable|integer|min:1',
            'agency_id' => 'nullable|exists:agencies,id',
            'agency_reference' => 'nullable|string',
            'agency_cost' => 'required|numeric|min:0',
            'client_price' => 'required|numeric|min:0',
        ]);

        $service = VisaUmrahService::create(array_merge($data, [
            'booking_id' => $booking->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => ucfirst($data['service_type']) . ' added successfully.',
        ]);
    }

    public function destroy($bookingId, $serviceId)
    {
        $service = VisaUmrahService::where('booking_id', $bookingId)->findOrFail($serviceId);
        $service->payments()->delete();
        $service->delete();

        return response()->json(['success' => true, 'message' => 'Service deleted.']);
    }
}