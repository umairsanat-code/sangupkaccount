<?php

namespace App\Http\Controllers;


use App\Models\Client;
use App\Models\Customer;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Enums\SellerDocumentEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Models\DailySaleTicket;
use App\Models\Agent;
use App\Models\Vendor;

class HomeController extends Controller
{

    /**
     * paidAmount : Aggregate column used to perform sum for order sale amount
     *
     * @var string
     */
    protected $paidAmount='paidAmount';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
  public function indexold()
{
    

    // Spain timezone
    $spainNow = Carbon::now('Europe/Madrid');
    $currentYear = $spainNow->year;
    $todayStart = $spainNow->copy()->startOfDay()->setTimezone('UTC');
    $todayEnd = $spainNow->copy()->endOfDay()->setTimezone('UTC');

    // --- Monthly ticket data ---
    $ticketsData = \DB::table('tickets')
        ->join('sales', 'tickets.id', '=', 'sales.ticket_id')
        ->selectRaw('
            MONTH(CONVERT_TZ(tickets.created_at,"+00:00","+02:00")) as month,
            COUNT(tickets.id) as total_tickets,
            SUM(sales.sale_price) as total_sales,
            SUM(tickets.purchase_price) as total_payments
        ')
        ->whereYear(\DB::raw('CONVERT_TZ(tickets.created_at,"+00:00","+02:00")'), $currentYear)
        ->groupByRaw('MONTH(CONVERT_TZ(tickets.created_at,"+00:00","+02:00"))')
        ->orderByRaw('MONTH(CONVERT_TZ(tickets.created_at,"+00:00","+02:00"))')
        ->get();

    $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $ticketsCount = array_fill(0, 12, 0);
    $salesAmount = array_fill(0, 12, 0);
    $profitAmount = array_fill(0, 12, 0);
    $lossAmount = array_fill(0, 12, 0);

    foreach ($ticketsData as $data) {
        $index = $data->month - 1;
        $ticketsCount[$index] = $data->total_tickets;
        $salesAmount[$index] = $data->total_sales;
        $monthlyDifference = $data->total_sales - $data->total_payments;
        $profitAmount[$index] = $monthlyDifference > 0 ? $monthlyDifference : 0;
        $lossAmount[$index] = $monthlyDifference < 0 ? abs($monthlyDifference) : 0;
    }

    // --- Monthly customers ---
    $customersData = \DB::table('tickets')
        ->selectRaw('MONTH(CONVERT_TZ(created_at,"+00:00","+02:00")) as month, COUNT(DISTINCT customer_id) as total_customers')
        ->whereYear(\DB::raw('CONVERT_TZ(created_at,"+00:00","+02:00")'), $currentYear)
        ->groupByRaw('MONTH(CONVERT_TZ(created_at,"+00:00","+02:00"))')
        ->orderByRaw('MONTH(CONVERT_TZ(created_at,"+00:00","+02:00"))')
        ->get();

    $customersCount = array_fill(0, 12, 0);
    foreach ($customersData as $data) {
        $index = $data->month - 1;
        $customersCount[$index] = $data->total_customers;
    }

    // --- Total tickets & customers today ---
    $totalTickets = \DB::table('tickets')
        ->count();

    $totalCustomers = DailySaleTicket::whereBetween('date', [$todayStart, $todayEnd])
        ->where('ticket_number', '!=', 0)
        ->count();

    $todayReservation = DailySaleTicket::whereBetween('date', [$todayStart, $todayEnd])
        ->where('ticket_number', 0)
        ->count();

    // --- Monthly profit & loss helper ---
    $profitAndLoss = $this->getMonthlyProfitAndLoss($currentYear, 12);

    // --- Available years for dropdown/filter ---
    $availableYears = \DB::table('tickets')
        ->selectRaw('YEAR(flight_date) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    // --- Team earnings ---
    $teamEarnings = User::with(['tickets.sales'])->get()->map(function ($member) {
        $totalSale = $member->tickets->flatMap->sales->sum('sale_price');
        $totalPurchase = $member->tickets->sum('purchase_price');
        $profit = $totalSale - $totalPurchase;

        return [
            'name' => $member->firstName . ' ' . $member->lastName,
            'earnings' => $totalSale,
            'purchases' => $totalPurchase,
            'profit' => $profit,
        ];
    });

    // --- Today's sales ---
    $todaySales = DailySaleTicket::whereBetween('date', [$todayStart, $todayEnd])
        ->sum('sale_price');

    // --- Pending payments ---
    $pendingPayments = \DB::table('payment_tickets')->sum('amount_paid');

    // --- Today's profit ---
    $todayProfit = $todaySales - DailySaleTicket::whereBetween('date', [$todayStart, $todayEnd])
        ->sum('issued_price');

    // --- Total expenses for current year ---
    $totalExpenses = \DB::table('expenses')
        ->whereYear('date', $currentYear)
        ->sum('amount');

    $netProfitLoss = $todayProfit - $totalExpenses;

    // --- Return to view ---
    return view('home', compact(
        'months', 'totalCustomers', 'totalTickets',
        'ticketsCount', 'salesAmount', 'profitAmount', 'lossAmount',
        'customersCount', 'profitAndLoss', 'availableYears', 'currentYear',
        'teamEarnings', 'todaySales', 'pendingPayments', 'todayProfit',
        'totalExpenses', 'netProfitLoss', 'todayReservation'
    ));
}

public function index(Request $request)
{
    // --- Spain timezone setup ---
    $spainNow = Carbon::now('Europe/Madrid');
    $currentYear = $spainNow->year;
    $startDate = $request->filled('start_date')
        ? Carbon::parse($request->start_date, 'Europe/Madrid')->startOfDay()
        : $spainNow->copy()->startOfDay();
    $endDate = $request->filled('end_date')
        ? Carbon::parse($request->end_date, 'Europe/Madrid')->endOfDay()
        : $spainNow->copy()->endOfDay();

    // --- Monthly ticket data ---
    $ticketsData = \DB::table('tickets')
        ->join('sales', 'tickets.id', '=', 'sales.ticket_id')
        ->selectRaw('
            MONTH(CONVERT_TZ(tickets.created_at,"+00:00","+02:00")) as month,
            COUNT(tickets.id) as total_tickets,
            SUM(sales.sale_price) as total_sales,
            SUM(tickets.purchase_price) as total_payments
        ')
        ->whereYear(\DB::raw('CONVERT_TZ(tickets.created_at,"+00:00","+02:00")'), $currentYear)
        ->groupByRaw('MONTH(CONVERT_TZ(tickets.created_at,"+00:00","+02:00"))')
        ->orderByRaw('MONTH(CONVERT_TZ(tickets.created_at,"+00:00","+02:00"))')
        ->get();

    $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $ticketsCount = array_fill(0, 12, 0);
    $salesAmount = array_fill(0, 12, 0);
    $profitAmount = array_fill(0, 12, 0);
    $lossAmount = array_fill(0, 12, 0);

    foreach ($ticketsData as $data) {
        $index = $data->month - 1;
        $ticketsCount[$index] = $data->total_tickets;
        $salesAmount[$index] = $data->total_sales;
        $profitAmount[$index] = $data->total_payments;
        $lossAmount[$index] = $data->total_sales - $data->total_payments;
    }

    // --- Monthly customers ---
    $customersData = \DB::table('tickets')
        ->selectRaw('MONTH(CONVERT_TZ(created_at,"+00:00","+02:00")) as month, COUNT(DISTINCT customer_id) as total_customers')
        ->whereYear(\DB::raw('CONVERT_TZ(created_at,"+00:00","+02:00")'), $currentYear)
        ->groupByRaw('MONTH(CONVERT_TZ(created_at,"+00:00","+02:00"))')
        ->orderByRaw('MONTH(CONVERT_TZ(created_at,"+00:00","+02:00"))')
        ->get();

    $customersCount = array_fill(0, 12, 0);
    foreach ($customersData as $data) {
        $index = $data->month - 1;
        $customersCount[$index] = $data->total_customers;
    }

    // --- Total tickets & customers today ---
    $totalTickets = \DB::table('tickets')
        ->whereBetween(\DB::raw('CONVERT_TZ(created_at,"+00:00","+02:00")'), [$startDate, $endDate])
        ->count();

    $totalCustomers = \App\Models\DailySaleTicket::whereBetween(
            \DB::raw('CONVERT_TZ(date,"+00:00","+02:00")'),
            [$startDate, $endDate]
        )
        ->where('ticket_number', '!=', 0)
        ->count();

    $todayReservation = \App\Models\DailySaleTicket::whereBetween(
            \DB::raw('CONVERT_TZ(date,"+00:00","+02:00")'),
            [$startDate, $endDate]
        )
        ->where('ticket_number', 0)
        ->count();

    // --- Monthly profit & loss helper ---
    $profitAndLoss = $this->getMonthlyProfitAndLoss($currentYear, 12);

    // --- Available years for dropdown/filter ---
    $availableYears = \DB::table('tickets')
        ->selectRaw('YEAR(CONVERT_TZ(flight_date,"+00:00","+02:00")) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    // --- Team earnings ---
    $teamEarnings = \App\Models\User::with(['tickets.sales'])->get()->map(function ($member) {
        $totalSale = $member->tickets->flatMap->sales->sum('sale_price');
        $totalPurchase = $member->tickets->sum('purchase_price');
        $profit = $totalSale - $totalPurchase;

        return [
            'name' => $member->firstName . ' ' . $member->lastName,
            'earnings' => $totalSale,
            'purchases' => $totalPurchase,
            'profit' => $profit,
        ];
    });

    // --- Today's sales (payments received on this date, including old tickets) ---
    $todaySales = \DB::table('daily_sale_ticket_payments')
        ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->sum('amount');

    // --- Pending payments ---
    $pendingPayments = $todaySales;

    // --- Total purchase cost (tickets issued in this date range) ---
    $totalPurchaseCost = \App\Models\DailySaleTicket::whereBetween(
            \DB::raw('CONVERT_TZ(date,"+00:00","+02:00")'),
            [$startDate, $endDate]
        )->sum('issued_price');

    // --- Today's profit (payments received in range - purchase cost of tickets in range) ---
    $todayProfit = $todaySales - $totalPurchaseCost;

    // --- Total expenses in selected range ---
    $totalExpenses = \DB::table('expenses')
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
        ->sum('amount');

    $cashPayments = \DB::table('daily_sale_ticket_payments')
        ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->whereRaw('LOWER(payment_method) = ?', ['cash'])
        ->sum('amount');

    $cardPayments = \DB::table('daily_sale_ticket_payments')
        ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->whereRaw('LOWER(payment_method) = ?', ['card'])
        ->sum('amount');

    $bankPayments = \DB::table('daily_sale_ticket_payments')
        ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->whereRaw('LOWER(payment_method) = ?', ['bank'])
        ->sum('amount');

    $netProfitLoss = $todayProfit - $totalExpenses;

    // --- Agent-wise Vendor Remaining (super admin only) ---
    $agentsVendorData = [];
    if (auth()->id() == '1') {
        $agentsVendorData = Agent::all()->map(function ($agent) use ($startDate, $endDate) {
            $ticketsQuery = Ticket::where('agent_id', $agent->id)
                ->where('refund_status', 'Not Refunded')
                ->whereHas('sales', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()]);
                });

            $totalPurchase = (clone $ticketsQuery)->sum('purchase_price');
            $paidAmount = Vendor::where('agent_id', $agent->id)->sum('payment_amount');
            $remainingAmount = $totalPurchase - $paidAmount;

            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'total_purchase' => number_format($totalPurchase, 2),
                'total_paid' => number_format($paidAmount, 2),
                'total_remaining' => number_format($remainingAmount, 2),
            ];
        })->values()->all();
    }

    // --- Return to view ---
    return view('home', compact(
        'months', 'totalCustomers', 'totalTickets',
        'ticketsCount', 'salesAmount', 'profitAmount', 'lossAmount',
        'customersCount', 'profitAndLoss', 'availableYears', 'currentYear',
        'teamEarnings', 'todaySales', 'pendingPayments', 'todayProfit',
        'totalExpenses', 'netProfitLoss', 'todayReservation',
        'startDate', 'endDate', 'cashPayments', 'cardPayments', 'bankPayments',
        'agentsVendorData'
    ));
}

 

public function getMonthlyProfitAndLoss($currentYear, $perPage = 10)
{
    // ✅ Get sales grouped by ticket and month
    $sales = \DB::table('tickets')
        ->join('sales', 'tickets.id', '=', 'sales.ticket_id')
        ->selectRaw('
            YEAR(sales.sale_date) as year,
            MONTH(sales.sale_date) as month,
            tickets.id as ticket_id,
            tickets.ticket_number,
            tickets.purchase_price,
            SUM(sales.sale_price) as total_sales,
            SUM(sales.service_charges) as total_service_charges,
            SUM(sales.airline_charges) as total_airline_charges,
            (SUM(sales.sale_price) - tickets.purchase_price - 
            (SUM(sales.service_charges) + SUM(sales.airline_charges))) as total_profit
        ')
        ->whereYear('sales.sale_date', $currentYear)
        ->groupBy('year', 'month', 'tickets.id', 'tickets.ticket_number', 'tickets.purchase_price')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    $sales = collect($sales);

    // ✅ Get expenses grouped by month
    $expenses = \DB::table('expenses')
        ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total_expenses')
        ->whereYear('date', $currentYear)
        ->groupBy('year', 'month')
        ->get();

    $expenses = collect($expenses);

    // ✅ Initialize array
    $profitAndLoss = [];

    for ($month = 1; $month <= 12; $month++) {
        // 🔹 Get all sales for this month
        $monthlySales = $sales->filter(function ($s) use ($month, $currentYear) {
            return $s->year == $currentYear && $s->month == $month;
        });

        $totalSales = $monthlySales->sum('total_sales');
        $totalProfit = $monthlySales->sum('total_profit');
          $totalPurchase = $monthlySales->sum('purchase_price');

        // 🔹 Get expenses for this month
        $expense = $expenses->first(function ($e) use ($month, $currentYear) {
            return $e->year == $currentYear && $e->month == $month;
        });

        $totalExpenses = $expense ? $expense->total_expenses : 0;

        // 🔹 Net Profit/Loss
        $profitOrLoss = $totalProfit - $totalExpenses;

        $profitAndLoss[] = [
            'year' => $currentYear,
            'month' => $month,
            'gross_profit' => $totalProfit,
            'expenses' => $totalExpenses,
            'net_profit_or_loss' => $profitOrLoss,
            'purchase_total' => $totalPurchase, 
        ];
    }

    // ✅ Sort by year & month
    $profitAndLoss = collect($profitAndLoss)->sortBy([
        ['year', 'asc'],
        ['month', 'asc'],
    ])->values();
// dd($profitAndLoss);
    // ✅ Pagination
    $currentPage = request()->get('page', 1);
    $offset = ($currentPage - 1) * $perPage;

    $paginatedData = new LengthAwarePaginator(
        $profitAndLoss->slice($offset, $perPage)->values(),
        $profitAndLoss->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return $paginatedData;
}


    public function fetchProfitAndLossData(Request $request)
    {
        $year = $request->input('year', now()->year);
        $profitAndLoss = $this->getMonthlyProfitAndLoss($year, 12);

        // Format data for the response
        $data = $profitAndLoss->map(function ($item) {
            return [
                'month_name' => \Carbon\Carbon::createFromDate($item['year'], $item['month'], 1)->format('F-Y'),
                'purchase_total' => $item['purchase_total'],
                'gross_profit' => $item['gross_profit'],
                'expenses' => $item['expenses'],
                'net_profit_or_loss' => $item['net_profit_or_loss'],
            ];
        });

        return response()->json($data);
    }

    public function fetchPaymentClearTickets(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date, 'Europe/Madrid')->startOfDay()
            : Carbon::now('Europe/Madrid')->startOfDay();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date, 'Europe/Madrid')->endOfDay()
            : Carbon::now('Europe/Madrid')->endOfDay();

        if (!auth()->user()->hasRole('admin')) {
            $tickets = DailySaleTicket::where('user_id', auth()->id())
                ->whereHas('payments', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()]);
                })->with(['payments' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()]);
                }])->get();
        } else {
            $tickets = DailySaleTicket::whereHas('payments', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })->with(['payments' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()]);
            }])->get();
        }

        $rows = $tickets->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'date' => $ticket->date ? Carbon::parse($ticket->date)->setTimezone('Europe/Madrid')->format('Y-m-d H:i') : '',
                'pnr' => $ticket->pnr,
                'pax_name' => $ticket->pax_name,
                'sale_price' => number_format($ticket->sale_price, 2),
                'paid_amount' => number_format($ticket->payments->sum('amount'), 2),
                'pending' => number_format($ticket->pendings, 2),
                'status' => $ticket->status,
                'payments' => $ticket->payments->map(function ($p) {
                    return [
                        'payment_date' => $p->payment_date,
                        'amount' => number_format($p->amount, 2),
                        'payment_method' => $p->payment_method,
                        'payment_stage' => $p->payment_stage,
                        'card_name' => $p->card_name,
                        'remarks' => $p->remarks,
                    ];
                }),
            ];
        })->values();

        return response()->json($rows);
    }

    
    public function index2()
    {

//        $allSellers = User::role('user')->count();//Total team members
////        dd($allSellers);
//        $allBuyers = Client::count();
//        $messageSent =Message::where('status','sent')->count();
//        $messageReceived =Message::where('status','received')->count();
////        dd($messageReceived,$messageSent);
//        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
//
//        $groupByMonthCount = Message::select(DB::raw("MONTHNAME(created_at) as month"), DB::raw("COUNT(*) as count"))
//            ->whereIn('status', ['received', 'sent'])
//            ->groupBy(DB::raw("MONTHNAME(created_at)"))
//            ->orderBy(DB::raw("MONTH(created_at)"))
//            ->get()->keyBy('month');
////        dd($groupByMonthCount);
////        $groupByMonthCount = $this->sumOrders($months);
//        $groupByMonthCountArray = array_fill_keys($months, 0);
//
//        foreach ($groupByMonthCount as $record) {
//            $groupByMonthCountArray[$record->month] = $record->count;
//        }

//        $totalTickets = Ticket::count(); // Replace with your query
//        $totalCustomers = Customer::count(); // Replace with your query
//
//        // Monthly tickets data
//        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
//
//        // Example data (replace with actual database queries)
//        $ticketsData = [10, 15, 12, 20, 18, 25, 30, 28, 22, 35, 40, 45];
//        $customersData = [5, 8, 10, 12, 18, 20, 22, 25, 30, 35, 40, 50];
//
//        // Recent tickets for table
//        $recentTickets = Ticket::orderBy('created_at', 'desc')->limit(10)->get(); // Adjust query as needed
//
//        return view('home', compact('totalTickets', 'totalCustomers', 'months', 'ticketsData', 'recentTickets','customersData'));

    }

     /**
     * sumOrders  count the user in a month and store it into array
     *
     * @param  mixed $months
     * @return array
     */
    public function sumOrders(array $months):array
    {
        $orders = Order::select(DB::raw('sum('.$this->paidAmount.') as sale'),
            DB::raw("DATE_FORMAT(DATE(FROM_UNIXTIME(createdAt)),'%M') as months"))
            ->whereIn('status', [OrderStatus::COMPLETED, OrderStatus::RATING])->groupBy('months')->get();
        $setValueMonthWise=array();
        $totalSumByMont =array();
        foreach ($orders as  $value) {
            $setValueMonthWise[$value->months]=$value->sale;
        }
        foreach ($months as  $month) {
            $value=0;
            if(isset($setValueMonthWise[$month])){
                $value=$setValueMonthWise[$month];
            }
            /* store if not exist the sum then set it 0 */
            $totalSumByMont[] = $value;
        }
        return $totalSumByMont;
    }

     public function getLastMonthSale(){
        $start = strtotime(Carbon::now()->subMonths(1)->startOfMonth());
        $end = strtotime(Carbon::now()->subMonth(1)->endOfMonth());
         $lastMonthSales = Order::select(DB::raw('sum('.$this->paidAmount.') as sale'))
             ->whereIn('status', [OrderStatus::COMPLETED, OrderStatus::RATING])
             ->whereBetween('createdAt',[$start,$end])->value('sale');
         $start = strtotime(Carbon::now()->startOfMonth());
         $end = strtotime(Carbon::now()->endOfMonth());
         $currentMonthSales = Order::select(DB::raw('sum('.$this->paidAmount.') as sale'))
             ->whereIn('status', [OrderStatus::COMPLETED, OrderStatus::RATING])
             ->whereBetween('createdAt',[$start,$end])->value('sale');
         $values=$currentMonthSales-$lastMonthSales;
         if($lastMonthSales == 0)
         {
             return 0;
         }
         return round(($values/$lastMonthSales)*100);
     }


}


