<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahPayment;
use App\Models\VisaUmrahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisaUmrahReportController extends Controller
{
    public function index(Request $request)
    {
        $filter = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'booking_type' => $request->booking_type,
            'visa_country' => $request->visa_country,
            'client_name' => $request->client_name,
            'agency_id' => $request->agency_id,
            'status' => $request->status,
        ];

        $bookings = $this->filteredBookings($request);

        // 1. Overall P&L
        $mainClient = (float) $bookings->sum('main_client_total');
        $servicesClient = (float) $bookings->sum(function ($b) {
            return $b->sc_sum ?? 0;
        });
        $mainAgency = (float) $bookings->sum('main_agency_cost');
        $servicesAgency = (float) $bookings->sum(function ($b) {
            return $b->sa_sum ?? 0;
        });
        $expenses = (float) $bookings->sum(function ($b) {
            return $b->ex_sum ?? 0;
        });
        $clientReceived = (float) $bookings->sum(function ($b) {
            return $b->cr_sum ?? 0;
        });
        $agencyPaid = (float) $bookings->sum(function ($b) {
            return $b->ap_sum ?? 0;
        });

        $overall = [
            'total_sales' => $mainClient + $servicesClient,
            'client_received' => $clientReceived,
            'client_pending' => ($mainClient + $servicesClient) - $clientReceived,
            'total_agency_payable' => $mainAgency + $servicesAgency,
            'agency_paid' => $agencyPaid,
            'agency_pending' => ($mainAgency + $servicesAgency) - $agencyPaid,
            'expenses' => $expenses,
            'gross_profit' => ($mainClient + $servicesClient) - ($mainAgency + $servicesAgency),
            'net_profit' => ($mainClient + $servicesClient) - ($mainAgency + $servicesAgency) - $expenses,
        ];

        // 2. Client ledger (per client)
        $clientLedger = $bookings->groupBy('client_name')->map(function ($group) {
            $client = $group->first();
            $total = (float) $group->sum('main_client_total') + (float) $group->sum(function ($b) {
                return $b->sc_sum ?? 0;
            });
            $received = (float) $group->sum(function ($b) {
                return $b->cr_sum ?? 0;
            });
            return [
                'client_name' => $client->client_name,
                'phone' => $client->client_phone,
                'bookings' => $group->count(),
                'total' => $total,
                'received' => $received,
                'pending' => $total - $received,
            ];
        })->values();

        // 3. Agency ledger
        $agencyLedger = [];
        foreach ($bookings as $b) {
            if (!$b->main_agency_id) continue;
            $key = $b->main_agency_id;
            if (!isset($agencyLedger[$key])) {
                $agencyLedger[$key] = [
                    'agency' => $b->mainAgency ? $b->mainAgency->name : 'Unknown',
                    'payable' => 0,
                    'paid' => 0,
                ];
            }
            $agencyLedger[$key]['payable'] += (float) $b->main_agency_cost + (float) ($b->sa_sum ?? 0);
            $agencyLedger[$key]['paid'] += (float) ($b->ap_sum ?? 0);
        }
        foreach ($agencyLedger as $k => $row) {
            $agencyLedger[$k]['pending'] = $row['payable'] - $row['paid'];
        }
        $agencyLedger = array_values($agencyLedger);

        // 4. Visit/Umrah profit breakdown rows
        foreach ($bookings as $b) {
            $b->client_total_full = (float) $b->main_client_total + (float) ($b->sc_sum ?? 0);
            $b->agency_total_full = (float) $b->main_agency_cost + (float) ($b->sa_sum ?? 0);
            $b->gross_profit = $b->client_total_full - $b->agency_total_full;
            $b->net_profit = $b->gross_profit - (float) ($b->ex_sum ?? 0);
        }

        // 5. Hotel / Transport profit
        $hotelServices = VisaUmrahService::with('booking')
            ->where('service_type', 'hotel')
            ->orderByDesc('created_at')
            ->get();
        $transportServices = VisaUmrahService::with('booking')
            ->where('service_type', 'transport')
            ->orderByDesc('created_at')
            ->get();

        // 6. Country list for filter
        $countries = VisaUmrahBooking::where('booking_type', 'visa')
            ->whereNotNull('visa_country')
            ->distinct()->orderBy('visa_country')->pluck('visa_country');

        $agencies = Agency::orderBy('name')->get();

        return view('admin.visa_umrah.reports', compact(
            'filter', 'bookings', 'overall', 'clientLedger', 'agencyLedger',
            'hotelServices', 'transportServices', 'countries', 'agencies'
        ));
    }

    protected function filteredBookings(Request $request)
    {
        $query = VisaUmrahBooking::with('mainAgency')
            ->withSum('services as sc_sum', 'client_price')
            ->withSum('services as sa_sum', 'agency_cost')
            ->withSum('clientPayments as cr_sum', 'amount')
            ->withSum('agencyPayments as ap_sum', 'amount')
            ->withSum('expenses as ex_sum', 'amount');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('booking_type')) {
            $query->where('booking_type', $request->booking_type);
        }
        if ($request->filled('visa_country')) {
            $query->where('visa_country', $request->visa_country);
        }
        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->client_name . '%');
        }
        if ($request->filled('agency_id')) {
            $query->where('main_agency_id', $request->agency_id);
        }
        if ($request->filled('status')) {
            $query->where('visa_status', $request->status);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function agenciesFilter()
    {
        return \Illuminate\Support\Facades\Response::json(Agency::orderBy('name')->get());
    }
}