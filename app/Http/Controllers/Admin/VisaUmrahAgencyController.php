<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisaUmrahAgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::withCount('mainBookings as booking_count')
            ->orderBy('name')
            ->get();

        foreach ($agencies as $agency) {
            $agency->agency_payable =
                (float) VisaUmrahBooking::where('main_agency_id', $agency->id)->sum('main_agency_cost')
                + (float) VisaUmrahService::where('agency_id', $agency->id)->sum('agency_cost');

            $agency->agency_paid = $this->agencyPaidTotal($agency->id);
            $agency->outstanding = $agency->agency_payable - $agency->agency_paid;
        }

        return view('admin.visa_umrah.agencies.index', compact('agencies'));
    }

    protected function agencyPaidTotal($agencyId)
    {
        $mainPaid = VisaUmrahBooking::where('main_agency_id', $agencyId)
            ->selectRaw('COALESCE((SELECT SUM(amount) FROM visa_umrah_payments WHERE booking_id = visa_umrah_bookings.id AND party = "agency" AND service_id IS NULL), 0) as paid')
            ->get()->sum('paid');

        $servicePaid = VisaUmrahService::where('agency_id', $agencyId)
            ->selectRaw('COALESCE((SELECT SUM(amount) FROM visa_umrah_payments WHERE service_id = visa_umrah_services.id AND party = "agency"), 0) as paid')
            ->get()->sum('paid');

        return $mainPaid + $servicePaid;
    }

    public function create()
    {
        return view('admin.visa_umrah.agencies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'email' => 'nullable|email',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'services' => 'nullable|array',
        ]);

        Agency::create(array_merge($data, [
            'services' => $data['services'] ?? [],
            'created_by' => Auth::id(),
        ]));

        toastr()->success('Agency created.');
        return redirect()->route('visa_umrah.agencies.index');
    }

    public function show($id)
    {
        $agency = Agency::findOrFail($id);

        $bookings = VisaUmrahBooking::with('mainAgency')
            ->where('main_agency_id', $id)
            ->withSum('services as services_client_sum', 'client_price')
            ->withSum('services as services_agency_sum', 'agency_cost')
            ->withSum('clientPayments as client_received_sum', 'amount')
            ->withSum('agencyPayments as agency_paid_sum', 'amount')
            ->orderByDesc('created_at')
            ->get();

        $services = VisaUmrahService::with('booking')
            ->where('agency_id', $id)
            ->orderByDesc('created_at')
            ->get();

        // Attach received/paid per service using model amounts
        $services->each(function ($svc) {
            $svc->client_received_sum = $svc->clientReceivedTotal();
            $svc->agency_paid_sum = $svc->agencyPaidTotal();
        });

        // Totals
        $mainAgencyTotal = (float) VisaUmrahBooking::where('main_agency_id', $id)->sum('main_agency_cost');
        $servicesAgencyTotal = (float) VisaUmrahService::where('agency_id', $id)->sum('agency_cost');

        $mainPaid = VisaUmrahBooking::where('main_agency_id', $id)
            ->selectRaw('COALESCE((SELECT SUM(amount) FROM visa_umrah_payments WHERE booking_id = visa_umrah_bookings.id AND party = "agency" AND service_id IS NULL), 0) as paid')
            ->get()->sum('paid');

        $servicePaid = VisaUmrahService::where('agency_id', $id)
            ->selectRaw('COALESCE((SELECT SUM(amount) FROM visa_umrah_payments WHERE service_id = visa_umrah_services.id AND party = "agency"), 0) as paid')
            ->get()->sum('paid');

        $totalPayable = $mainAgencyTotal + $servicesAgencyTotal;
        $totalPaid = $mainPaid + $servicePaid;

        return view('admin.visa_umrah.agencies.show', compact(
            'agency', 'bookings', 'services', 'totalPayable', 'totalPaid'
        ));
    }

    public function edit($id)
    {
        $agency = Agency::findOrFail($id);
        $allServices = ['visa', 'umrah', 'hotel', 'transport', 'tickets'];
        return view('admin.visa_umrah.agencies.edit', compact('agency', 'allServices'));
    }

    public function update(Request $request, $id)
    {
        $agency = Agency::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string',
            'contact_person' => 'nullable|string',
            'phone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'email' => 'nullable|email',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'services' => 'nullable|array',
        ]);

        $agency->update(array_merge($data, [
            'services' => $data['services'] ?? [],
        ]));

        toastr()->success('Agency updated.');
        return redirect()->route('visa_umrah.agencies.show', $agency->id);
    }

    public function destroy($id)
    {
        Agency::findOrFail($id)->delete();
        toastr()->success('Agency deleted.');
        return redirect()->route('visa_umrah.agencies.index');
    }
}