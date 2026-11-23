<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VisaUmrahDashboardController extends Controller
{
    public function index()
    {
        $totalVisa = VisaUmrahBooking::where('booking_type', 'visa')->count();
        $pendingVisa = VisaUmrahBooking::where('booking_type', 'visa')
            ->where('visa_status', 'pending')->count();
        $approvedVisa = VisaUmrahBooking::where('booking_type', 'visa')
            ->where('visa_status', 'approved')->count();

        $totalUmrah = VisaUmrahBooking::where('booking_type', 'umrah')->count();
        $activeUmrah = VisaUmrahBooking::where('booking_type', 'umrah')
            ->where('booking_status', 'active')->count();

        $totalClients = VisaUmrahBooking::distinct('client_name')->count('client_name');

        // Sales / received figures
        $mainClientTotal = (float) VisaUmrahBooking::sum('main_client_total');
        $servicesClientTotal = (float) DB::table('visa_umrah_services')->sum('client_price');
        $totalSales = $mainClientTotal + $servicesClientTotal;

        $clientReceived = (float) VisaUmrahPayment::where('party', 'client')->sum('amount');
        $clientTotalOverall = $mainClientTotal + $servicesClientTotal;
        $clientPending = $clientTotalOverall - $clientReceived;

        $mainAgencyCost = (float) VisaUmrahBooking::sum('main_agency_cost');
        $servicesAgencyCost = (float) DB::table('visa_umrah_services')->sum('agency_cost');
        $totalAgencyPayable = $mainAgencyCost + $servicesAgencyCost;

        $agencyPaid = (float) VisaUmrahPayment::where('party', 'agency')->sum('amount');

        // Expense total
        $totalExpenses = (float) DB::table('visa_umrah_expenses')->sum('amount');

        $totalProfit = $totalSales - $totalAgencyPayable - $totalExpenses;

        // This month profit (bookings created this month -> current profit calc)
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthMainClient = (float) VisaUmrahBooking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('main_client_total');
        $monthServicesClient = (float) DB::table('visa_umrah_services')
            ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('client_price');
        $monthMainAgency = (float) VisaUmrahBooking::whereBetween('created_at', [$monthStart, $monthEnd])->sum('main_agency_cost');
        $monthServicesAgency = (float) DB::table('visa_umrah_services')
            ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('agency_cost');
        $monthExpenses = (float) DB::table('visa_umrah_expenses')
            ->whereBetween('expense_date', [$monthStart, $monthEnd])
            ->orWhereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthProfit = ($monthMainClient + $monthServicesClient)
            - ($monthMainAgency + $monthServicesAgency)
            - $monthExpenses;

        // Chart data
        $countryWise = VisaUmrahBooking::where('booking_type', 'visa')
            ->whereNotNull('visa_country')
            ->select('visa_country', DB::raw('COUNT(*) as total'))
            ->groupBy('visa_country')
            ->orderByDesc('total')
            ->get();

        $agencyWise = VisaUmrahBooking::whereNotNull('main_agency_id')
            ->select('main_agency_id', DB::raw('COUNT(*) as total'))
            ->groupBy('main_agency_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $a = Agency::find($row->main_agency_id);
                $row->agency_name = $a ? $a->name : 'Unknown';
                return $row;
            });

        // Monthly sales (last 6 months)
        $monthlySales = [];
        $monthlyProfit = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->startOfMonth()->subMonths($i);
            $end = Carbon::now()->endOfMonth()->subMonths($i);

            $mc = (float) VisaUmrahBooking::whereBetween('created_at', [$start, $end])->sum('main_client_total');
            $sc = (float) DB::table('visa_umrah_services')->whereBetween('created_at', [$start, $end])->sum('client_price');
            $ma = (float) VisaUmrahBooking::whereBetween('created_at', [$start, $end])->sum('main_agency_cost');
            $sa = (float) DB::table('visa_umrah_services')->whereBetween('created_at', [$start, $end])->sum('agency_cost');
            $ex = (float) DB::table('visa_umrah_expenses')->whereBetween('created_at', [$start, $end])->sum('amount');

            $monthlySales[] = round($mc + $sc, 2);
            $monthlyProfit[] = round(($mc + $sc) - ($ma + $sa) - $ex, 2);
        }
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthLabels[] = Carbon::now()->startOfMonth()->subMonths($i)->format('M Y');
        }

        // Pending lists
        $pendingClientPayments = VisaUmrahBooking::with('mainAgency')
            ->select('visa_umrah_bookings.*')
            ->withSum('services as sc_sum', 'client_price')
            ->withSum('clientPayments as cr_sum', 'amount')
            ->get()
            ->filter(function ($b) {
                return ($b->main_client_total + (float) $b->sc_sum) - (float) $b->cr_sum > 0;
            })
            ->values()
            ->take(10);

        $pendingAgencyPayments = VisaUmrahBooking::with('mainAgency')
            ->select('visa_umrah_bookings.*')
            ->withSum('services as sa_sum', 'agency_cost')
            ->withSum('agencyPayments as ap_sum', 'amount')
            ->get()
            ->filter(function ($b) {
                return ($b->main_agency_cost + (float) $b->sa_sum) - (float) $b->ap_sum > 0;
            })
            ->values()
            ->take(10);

        return view('admin.visa_umrah.dashboard', compact(
            'totalVisa', 'pendingVisa', 'approvedVisa',
            'totalUmrah', 'activeUmrah', 'totalClients',
            'totalSales', 'clientReceived', 'clientPending',
            'totalAgencyPayable', 'agencyPaid', 'totalProfit', 'monthProfit',
            'countryWise', 'agencyWise', 'monthlySales', 'monthlyProfit', 'monthLabels',
            'pendingClientPayments', 'pendingAgencyPayments',
        ));
    }
}