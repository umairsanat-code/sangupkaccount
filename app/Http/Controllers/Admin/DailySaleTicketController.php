<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailySaleTicket;
use App\Models\DailySaleTicketPayment;
use App\Models\Expense;
use App\Models\Vendor;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;


class DailySaleTicketController extends Controller
{
    use LogsActivity;


    public function index_old(Request $request)
    {
        if ($request->ajax()) {

            $query = DailySaleTicket::query();

            /*
            |--------------------------------------------------------------------------
            | Date Filter
            |--------------------------------------------------------------------------
            */

            $hasDateFilter = !empty($request->start_date) && !empty($request->end_date);

            if ($hasDateFilter) {
                $startUTC = Carbon::parse(
                    $request->start_date,
                    'Europe/Madrid'
                )->startOfDay()->timezone('UTC');

                $endUTC = Carbon::parse(
                    $request->end_date,
                    'Europe/Madrid'
                )->endOfDay()->timezone('UTC');

                $query->whereBetween('date', [$startUTC, $endUTC]);
            } else {
                // Default: today's tickets (Spain timezone) for date-wise summary
                $todaySpain = Carbon::now('Europe/Madrid')->toDateString();
                $startUTC = Carbon::parse($todaySpain, 'Europe/Madrid')->startOfDay()->timezone('UTC');
                $endUTC = Carbon::parse($todaySpain, 'Europe/Madrid')->endOfDay()->timezone('UTC');
                $query->whereBetween('date', [$startUTC, $endUTC]);
            }

            $query->orderBy('created_at', 'desc');

            /*
            |--------------------------------------------------------------------------
            | Summary Queries
            |--------------------------------------------------------------------------
            */

            $summaryQuery = clone $query;

            // Purchase Total
            $purchaseTotal = (clone $summaryQuery)
                ->sum('issued_price');

            // Sale Total
            $saleTotal = (clone $summaryQuery)
                ->sum('sale_price');

            // Pending Total
            $pendingTotal = (clone $summaryQuery)
                ->sum('pendings');

            // Payment Type Totals
            $cardTotal = (clone $summaryQuery)
                ->whereRaw('LOWER(type) = ?', ['card'])
                ->sum('payment');

            $bankTotal = (clone $summaryQuery)
                ->whereRaw('LOWER(type) = ?', ['bank'])
                ->sum('payment');

            $cashTotal = (clone $summaryQuery)
                ->whereRaw('LOWER(type) = ?', ['cash'])
                ->sum('payment');

            /*
            |--------------------------------------------------------------------------
            | Expense Total
            |--------------------------------------------------------------------------
            */

            $expenseQuery = Expense::query();

            if ($hasDateFilter) {
                $expenseStart = Carbon::parse($request->start_date, 'Europe/Madrid')->format('Y-m-d');
                $expenseEnd = Carbon::parse($request->end_date, 'Europe/Madrid')->format('Y-m-d');
            } else {
                $expenseStart = Carbon::now('Europe/Madrid')->format('Y-m-d');
                $expenseEnd = $expenseStart;
            }

            $expenseQuery->whereBetween('date', [$expenseStart, $expenseEnd]);
            $expenseTotal = $expenseQuery->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Profit Calculation (date-wise)
            |--------------------------------------------------------------------------
            | Gross = Sale - Purchase
            | Net   = Gross - Expenses (e.g. sale 500, expense 300 => net 200)
            */

            $grossProfit = $saleTotal - $purchaseTotal;
            $netProfit = $grossProfit - $expenseTotal;

            /*
            |--------------------------------------------------------------------------
            | Vendor Summary
            |--------------------------------------------------------------------------
            */

            $vendorPurchaseTotal = (clone $summaryQuery)
                ->sum('issued_price');

            $vendorPaidTotal = Vendor::sum('payment_amount');

            $vendorRemainingTotal = Vendor::sum('remaining_amount');

            $vendorOldAmount = Vendor::whereDate(
                'created_at',
                '<',
                now()->toDateString()
            )->sum('remaining_amount');

            $vendorPending = $vendorPurchaseTotal - $vendorPaidTotal;

            /*
            |--------------------------------------------------------------------------
            | Remaining Payment Clear Summary
            |--------------------------------------------------------------------------
            */

            $paymentStartDate = $hasDateFilter
                ? $request->start_date
                : Carbon::now('Europe/Madrid')->toDateString();
            $paymentEndDate = $hasDateFilter
                ? $request->end_date
                : Carbon::now('Europe/Madrid')->toDateString();

            $clearPaymentTotal = DB::table('daily_sale_ticket_payments')
                ->whereBetween('payment_date', [$paymentStartDate, $paymentEndDate])
                ->sum('amount');

            $pendingClearTotal = DailySaleTicket::where('pendings', '>', 0)->sum('pendings');

            /*
            |--------------------------------------------------------------------------
            | Datatable
            |--------------------------------------------------------------------------
            */

            return DataTables::of($query)

                ->addIndexColumn()

                ->setRowClass(function ($row) {

                    if ($row->status == 'booked') {
                        return 'table-danger';
                    }

                    if ($row->status == 'issue') {
                        return 'table-success';
                    }

                    if ($row->status == 'reissued') {
                        return 'table-warning';
                    }

                    if ($row->status == 'cancel') {
                        return 'table-info';
                    }

                    return '';
                })

                ->addColumn('action', function ($row) {

                    $btn = '<div class="d-flex gap-1">';

                    // View Button
                    $btn .= '
                    <button class="btn btn-xs btn-info view-details"
                        data-id="'.$row->id.'"
                        title="View Details">

                        <i class="fas fa-eye"></i>
                    </button>
                ';

                    // Edit Button
                    $btn .= '
                    <a href="' . route('daily_report.edit', $row->id) . '"
                        class="btn btn-xs btn-primary"
                        title="Edit">

                        <i class="fas fa-edit"></i>
                    </a>
                ';

                    // Invoice Button
                    $btn .= '
                    <a href="' . route('daily_report.show', $row->id) . '"
                        class="btn btn-xs btn-success"
                        title="Invoice">

                        <i class="fas fa-file-invoice"></i>
                    </a>
                ';

                    // Add Payment Button
                    $btn .= '
                    <button class="btn btn-xs btn-warning add-payment"
                        data-id="'.$row->id.'"
                        title="Add Payment">

                        <i class="fas fa-money-bill"></i>
                    </button>
                ';

                    // History Button
                    $btn .= '
                    <button class="btn btn-xs btn-secondary view-history"
                        data-id="'.$row->id.'"
                        title="Payment History">

                        <i class="fas fa-history"></i>
                    </button>
                ';

                    // Status Dropdown
                    $statuses = ['issue', 'reissue', 'booked', 'cancel'];

                    $btn .= '
                    <select
                        class="form-control form-control-sm status-select ml-1 mr-1"
                        data-id="' . $row->id . '"
                        style="width:auto; min-width:110px;">
                ';

                    foreach ($statuses as $status) {

                        $selected = $row->status === $status
                            ? 'selected'
                            : '';

                        $btn .= '
                        <option value="' . $status . '" ' . $selected . '>
                            ' . ucfirst($status) . '
                        </option>
                    ';
                    }

                    $btn .= '</select>';

                    // Add Ticket Button
                    if (empty($row->ticket_number) || $row->ticket_number == 0) {

                        $btn .= '
                        <button class="btn btn-xs btn-info add-ticket"
                            data-id="' . $row->id . '"
                            title="Add Ticket Number">

                            <i class="fas fa-ticket-alt"></i>
                        </button>
                    ';
                    }

                    // Delete Button
                    $btn .= '
                    <form
                        action="' . route('daily_report.destroy', $row->id) . '"
                        method="POST"
                        style="display:inline-block;"
                        onsubmit="return confirm(\'Are you sure you want to delete this ticket?\');">

                        ' . csrf_field() . '
                        ' . method_field("DELETE") . '

                        <button
                            type="submit"
                            class="btn btn-xs btn-danger"
                            title="Delete">

                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                ';

                    $btn .= '</div>';

                    return $btn;
                })

                ->addColumn('added_by', function ($row) {

                    $user = User::find($row->user_id);

                    return $user
                        ? $user->firstName . ' ' . $user->lastName
                        : 'none';
                })

                ->addColumn('remarks', function ($row) {

                    $words = explode(" ", $row->remarks);

                    $shortNote = implode(
                        " ",
                        array_slice($words, 0, 3)
                    );

                    return '
                    <span>' . $shortNote . '</span>

                    <button
                        class="btn btn-primary btn-sm view-note"
                        data-toggle="modal"
                        data-target="#noteModal"
                        data-remarks="' . htmlspecialchars($row->remarks) . '">

                        More
                    </button>
                ';
                })

                ->addColumn('time_limit', function ($row) {

                    return Carbon::parse($row->time_limit)
                        ->setTimezone('Europe/Madrid')
                        ->addHours(2)
                        ->format('Y-m-d H:i');
                })

                ->addColumn('date', function ($row) {

                    return Carbon::parse($row->date)
                        ->timezone('Europe/Madrid')
                        ->format('Y-m-d H:i');
                })

                ->addColumn('status', function ($row) {

                    $statuses = [
                        'issue',
                        'cancel',
                        'booked',
                        'reissue'
                    ];

                    $select = '
                    <select
                        class="form-select form-select-sm status-dropdown"
                        data-id="'.$row->id.'">
                ';

                    foreach ($statuses as $status) {

                        $selected = (
                            $row->status == $status
                        ) ? 'selected' : '';

                        $select .= '
                        <option value="'.$status.'" '.$selected.'>
                            '.ucfirst($status).'
                        </option>
                    ';
                    }

                    $select .= '</select>';

                    return $select;
                })

                ->rawColumns([
                    'action',
                    'remarks',
                    'time_limit',
                    'date',
                    'status'
                ])

                ->with([

                    'summary' => [

                        'purchase' => number_format(
                            $purchaseTotal,
                            2
                        ),

                        'sale' => number_format(
                            $saleTotal,
                            2
                        ),

                        'expense' => number_format(
                            $expenseTotal,
                            2
                        ),

                        'pending' => number_format(
                            $pendingTotal,
                            2
                        ),

                        'vendorPaid' => number_format(
                            $vendorPaidTotal,
                            2
                        ),

                        'vendorBalance' => number_format(
                            $vendorPurchaseTotal - $vendorPaidTotal,
                            2
                        ),

                        'grossProfit' => number_format(
                            $grossProfit,
                            2
                        ),

                        'profit' => number_format(
                            $grossProfit,
                            2
                        ),

                        'netProfit' => number_format(
                            $netProfit,
                            2
                        ),

                        'cardTotal' => number_format(
                            $cardTotal,
                            2
                        ),

                        'bankTotal' => number_format(
                            $bankTotal,
                            2
                        ),

                        'cashTotal' => number_format(
                            $cashTotal,
                            2
                        ),

                        'vendorPurchase' => number_format(
                            $vendorPurchaseTotal,
                            2
                        ),

                        'vendorPaidTotal' => number_format(
                            $vendorPaidTotal,
                            2
                        ),

                        'vendorRemaining' => number_format(
                            $vendorRemainingTotal,
                            2
                        ),

                        'vendorOld' => number_format(
                            $vendorOldAmount,
                            2
                        ),

                        'vendorPending' => number_format(
                            $vendorPending,
                            2
                        ),

                        'clearPaymentTotal' => number_format(
                            $clearPaymentTotal,
                            2
                        ),

                        'pendingClearTotal' => number_format(
                            $pendingClearTotal,
                            2
                        ),
                    ]
                ])

                ->make(true);
        }

        return view('admin.daily_report.index');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = DailySaleTicket::query();

            /*
            |--------------------------------------------------------------------------
            | Role Based Filter (Non-admin => sirf apni tickets)
            |--------------------------------------------------------------------------
            */
            if (!auth()->user()->hasRole('admin')) {
                $query->where('user_id', auth()->id());
            }

            /*
            |--------------------------------------------------------------------------
            | Date Filter
            |--------------------------------------------------------------------------
            */

            $hasDateFilter = !empty($request->start_date) && !empty($request->end_date);

            if ($hasDateFilter) {
                $startUTC = Carbon::parse(
                    $request->start_date,
                    'Europe/Madrid'
                )->startOfDay()->timezone('UTC');

                $endUTC = Carbon::parse(
                    $request->end_date,
                    'Europe/Madrid'
                )->endOfDay()->timezone('UTC');

                $query->whereBetween('date', [$startUTC, $endUTC]);
            } else {
                // Default: today's tickets (Spain timezone) for date-wise summary
                $todaySpain = Carbon::now('Europe/Madrid')->toDateString();
                $startUTC = Carbon::parse($todaySpain, 'Europe/Madrid')->startOfDay()->timezone('UTC');
                $endUTC = Carbon::parse($todaySpain, 'Europe/Madrid')->endOfDay()->timezone('UTC');
                $query->whereBetween('date', [$startUTC, $endUTC]);
            }

            // Summary query before ordering (full date range, no status filter)
            $summaryQuery = clone $query;

            /*
            |--------------------------------------------------------------------------
            | Status Filter (for tabs: issue, reissue, booked, cancel)
            |--------------------------------------------------------------------------
            */
            if ($request->filled('status') && in_array($request->status, ['issue', 'reissue', 'booked', 'cancel'])) {
                $query->where('status', $request->status);
            }

            $query->orderBy('created_at', 'desc');

            /*
            |--------------------------------------------------------------------------
            | Summary Queries
            |--------------------------------------------------------------------------
            */

            // Purchase Total
            $purchaseTotal = (clone $summaryQuery)
                ->sum('issued_price');

            // Sale Total
            $saleTotal = (clone $summaryQuery)
                ->sum('sale_price');

            // Pending Total
            $pendingTotal = (clone $summaryQuery)
                ->sum('pendings');

            // Payment Type Totals
            $cardTotal = (clone $summaryQuery)
                ->whereRaw('LOWER(type) = ?', ['card'])
                ->sum('payment');

            $bankTotal = (clone $summaryQuery)
                ->whereRaw('LOWER(type) = ?', ['bank'])
                ->sum('payment');

            $cashTotal = (clone $summaryQuery)
                ->whereRaw('LOWER(type) = ?', ['cash'])
                ->sum('payment');

            /*
            |--------------------------------------------------------------------------
            | Expense Total
            |--------------------------------------------------------------------------
            */

            $expenseQuery = Expense::query();

            if ($hasDateFilter) {
                $expenseStart = Carbon::parse($request->start_date, 'Europe/Madrid')->format('Y-m-d');
                $expenseEnd = Carbon::parse($request->end_date, 'Europe/Madrid')->format('Y-m-d');
            } else {
                $expenseStart = Carbon::now('Europe/Madrid')->format('Y-m-d');
                $expenseEnd = $expenseStart;
            }

            $expenseQuery->whereBetween('date', [$expenseStart, $expenseEnd]);
            $expenseTotal = $expenseQuery->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Profit Calculation (date-wise)
            |--------------------------------------------------------------------------
            | Gross = Sale - Purchase
            | Net   = Gross - Expenses (e.g. sale 500, expense 300 => net 200)
            */

            $grossProfit = $saleTotal - $purchaseTotal;
            $netProfit = $grossProfit - $expenseTotal;

            /*
            |--------------------------------------------------------------------------
            | Vendor Summary
            |--------------------------------------------------------------------------
            */

            $vendorPurchaseTotal = (clone $summaryQuery)
                ->sum('issued_price');

            $vendorPaidTotal = Vendor::sum('payment_amount');

            $vendorRemainingTotal = Vendor::sum('remaining_amount');

            $vendorOldAmount = Vendor::whereDate(
                'created_at',
                '<',
                now()->toDateString()
            )->sum('remaining_amount');

            $vendorPending = $vendorPurchaseTotal - $vendorPaidTotal;

            /*
            |--------------------------------------------------------------------------
            | Remaining Payment Clear Summary
            |--------------------------------------------------------------------------
            */

            $paymentStartDate = $hasDateFilter
                ? $request->start_date
                : Carbon::now('Europe/Madrid')->toDateString();
            $paymentEndDate = $hasDateFilter
                ? $request->end_date
                : Carbon::now('Europe/Madrid')->toDateString();

            $clearPaymentTotal = DB::table('daily_sale_ticket_payments')
                ->whereBetween('payment_date', [$paymentStartDate, $paymentEndDate])
                ->sum('amount');

            $pendingClearTotal = DailySaleTicket::where('pendings', '>', 0)->sum('pendings');

            /*
            |--------------------------------------------------------------------------
            | Datatable
            |--------------------------------------------------------------------------
            */

            return DataTables::of($query)

                ->addIndexColumn()

                ->setRowClass(function ($row) {

                    if ($row->status == 'booked') {
                        return 'table-danger';
                    }

                    if ($row->status == 'issue') {
                        return 'table-success';
                    }

                    if ($row->status == 'reissued') {
                        return 'table-warning';
                    }

                    if ($row->status == 'cancel') {
                        return 'table-info';
                    }

                    return '';
                })

                ->addColumn('action', function ($row) {

                    $btn = '<div class="d-flex gap-1">';

                    // View Button
                    $btn .= '
            <button class="btn btn-xs btn-info view-details"
                data-id="'.$row->id.'"
                title="View Details">

                <i class="fas fa-eye"></i>
            </button>
        ';

                    // Edit Button
                    $btn .= '
            <a href="' . route('daily_report.edit', $row->id) . '"
                class="btn btn-xs btn-primary"
                title="Edit">

                <i class="fas fa-edit"></i>
            </a>
        ';

                    // Invoice Button
                    $btn .= '
            <a href="' . route('daily_report.show', $row->id) . '"
                class="btn btn-xs btn-success"
                title="Invoice">

                <i class="fas fa-file-invoice"></i>
            </a>
        ';

                    // Add Payment Button
                    $btn .= '
            <button class="btn btn-xs btn-warning add-payment"
                data-id="'.$row->id.'"
                title="Add Payment">

                <i class="fas fa-money-bill"></i>
            </button>
        ';

                    // History Button
                    $btn .= '
            <button class="btn btn-xs btn-secondary view-history"
                data-id="'.$row->id.'"
                title="Payment History">

                <i class="fas fa-history"></i>
            </button>
        ';

                    // Status Dropdown
                    $statuses = ['issue', 'reissue', 'booked', 'cancel'];

                    $btn .= '
            <select
                class="form-control form-control-sm status-select ml-1 mr-1"
                data-id="' . $row->id . '"
                style="width:auto; min-width:110px;">
        ';

                    foreach ($statuses as $status) {

                        $selected = $row->status === $status
                            ? 'selected'
                            : '';

                        $btn .= '
                <option value="' . $status . '" ' . $selected . '>
                    ' . ucfirst($status) . '
                </option>
            ';
                    }

                    $btn .= '</select>';

                    // Add Ticket Button
                    if (empty($row->ticket_number) || $row->ticket_number == 0) {

                        $btn .= '
                <button class="btn btn-xs btn-info add-ticket"
                    data-id="' . $row->id . '"
                    title="Add Ticket Number">

                    <i class="fas fa-ticket-alt"></i>
                </button>
            ';
                    }

                    // Delete Button
                    $btn .= '
            <form
                action="' . route('daily_report.destroy', $row->id) . '"
                method="POST"
                style="display:inline-block;"
                onsubmit="return confirm(\'Are you sure you want to delete this ticket?\');">

                ' . csrf_field() . '
                ' . method_field("DELETE") . '

                <button
                    type="submit"
                    class="btn btn-xs btn-danger"
                    title="Delete">

                    <i class="fas fa-trash"></i>
                </button>
            </form>
        ';

                    $btn .= '</div>';

                    return $btn;
                })

                ->addColumn('added_by', function ($row) {

                    $user = User::find($row->user_id);

                    return $user
                        ? $user->firstName . ' ' . $user->lastName
                        : 'none';
                })

                ->addColumn('remarks', function ($row) {

                    $words = explode(" ", $row->remarks);

                    $shortNote = implode(
                        " ",
                        array_slice($words, 0, 3)
                    );

                    return '
            <span>' . $shortNote . '</span>

            <button
                class="btn btn-primary btn-sm view-note"
                data-toggle="modal"
                data-target="#noteModal"
                data-remarks="' . htmlspecialchars($row->remarks) . '">

                More
            </button>
        ';
                })

                ->addColumn('time_limit', function ($row) {

                    return Carbon::parse($row->time_limit)
                        ->setTimezone('Europe/Madrid')
                        ->addHours(2)
                        ->format('Y-m-d H:i');
                })

                ->addColumn('date', function ($row) {

                    return Carbon::parse($row->date)
                        ->timezone('Europe/Madrid')
                        ->format('Y-m-d H:i');
                })

                ->addColumn('status', function ($row) {

                    $statuses = [
                        'issue',
                        'cancel',
                        'booked',
                        'reissue'
                    ];

                    $select = '
            <select
                class="form-select form-select-sm status-dropdown"
                data-id="'.$row->id.'">
        ';

                    foreach ($statuses as $status) {

                        $selected = (
                            $row->status == $status
                        ) ? 'selected' : '';

                        $select .= '
                <option value="'.$status.'" '.$selected.'>
                    '.ucfirst($status).'
                </option>
            ';
                    }

                    $select .= '</select>';

                    return $select;
                })

                ->rawColumns([
                    'action',
                    'remarks',
                    'time_limit',
                    'date',
                    'status'
                ])

                ->with([

                    'summary' => [

                        'purchase' => number_format(
                            $purchaseTotal,
                            2
                        ),

                        'sale' => number_format(
                            $saleTotal,
                            2
                        ),

                        'expense' => number_format(
                            $expenseTotal,
                            2
                        ),

                        'pending' => number_format(
                            $pendingTotal,
                            2
                        ),

                        'vendorPaid' => number_format(
                            $vendorPaidTotal,
                            2
                        ),

                        'vendorBalance' => number_format(
                            $vendorPurchaseTotal - $vendorPaidTotal,
                            2
                        ),

                        'grossProfit' => number_format(
                            $grossProfit,
                            2
                        ),

                        'profit' => number_format(
                            $grossProfit,
                            2
                        ),

                        'netProfit' => number_format(
                            $netProfit,
                            2
                        ),

                        'cardTotal' => number_format(
                            $cardTotal,
                            2
                        ),

                        'bankTotal' => number_format(
                            $bankTotal,
                            2
                        ),

                        'cashTotal' => number_format(
                            $cashTotal,
                            2
                        ),

                        'vendorPurchase' => number_format(
                            $vendorPurchaseTotal,
                            2
                        ),

                        'vendorPaidTotal' => number_format(
                            $vendorPaidTotal,
                            2
                        ),

                        'vendorRemaining' => number_format(
                            $vendorRemainingTotal,
                            2
                        ),

                        'vendorOld' => number_format(
                            $vendorOldAmount,
                            2
                        ),

                        'vendorPending' => number_format(
                            $vendorPending,
                            2
                        ),

                        'clearPaymentTotal' => number_format(
                            $clearPaymentTotal,
                            2
                        ),

                        'pendingClearTotal' => number_format(
                            $pendingClearTotal,
                            2
                        ),
                    ]
                ])

                ->make(true);
        }

        return view('admin.daily_report.index');
    }
    public function indexWithTicket(Request $request)
{

    if ($request->ajax()) {
        $query = DailySaleTicket::query()
            ->whereNotNull('ticket_number')
            ->where('ticket_number', '!=', 0);

        // Filter by date if provided
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $startUTC = Carbon\Carbon::parse($request->start_date, 'Europe/Madrid')->timezone('UTC')->startOfDay();
            $endUTC = Carbon\Carbon::parse($request->end_date, 'Europe/Madrid')->timezone('UTC')->endOfDay();
            $query->whereBetween('time_limit', [$startUTC, $endUTC]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<a href="' . route('daily_report.show', $row->id) . '" class="btn btn-sm" style="background:#007bff; color:white; border-radius:5px;">Invoice</a> ';
                $btn .= '<form action="' . route('daily_report.destroy', $row->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="btn btn-sm" style="background:#dc3545; color:white; border-radius:5px; border:none;">Delete</button>
                         </form>';
                return $btn;
            })
            ->addColumn('added_by', function ($row) {
                $user = User::find($row->user_id);
                return $user ? $user->firstName . ' ' . $user->lastName : 'none';
            })
            ->addColumn('date', function ($row) {
                return Carbon\Carbon::parse($row->date)->timezone('Europe/Madrid')->format('Y-m-d H:i');
            })
            ->addColumn('time_limit', function ($row) {
                return Carbon\Carbon::parse($row->time_limit)
                    ->setTimezone('Europe/Madrid')
                    ->addHours(2)
                    ->format('Y-m-d H:i');
            })
            ->addColumn('ticket_number', function ($row) {
                return '<span class="badge badge-success">🎫 ' . $row->ticket_number . '</span>';
            })
            ->setRowClass('table-success')
            ->rawColumns(['action', 'added_by', 'ticket_number'])
            ->make(true);
    }

    return view('admin.daily_report.index_with_ticket');
}



    public function create()
    {
        return view('admin.daily_report.create');
    }

   public function store(Request $request)
{
//    dd($request->all());
//    $data = $request->validate([
//        'date' => 'required|date',
//        'pnr' => 'required|unique:daily_sale_tickets',
//        'system' => 'required',
//        'issued_price' => 'nullable|numeric',
//        'pax_name' => 'required',
//        'sale_price' => 'required|numeric',
//        'payment' => 'required',
//        'payment_method' => 'required',
//        'time_limit' => 'required|date',
//        'ticket_number' => 'nullable',
//        'remarks' => 'required',
//        'type' => 'nullable'
//    ]);
       $data = $request->validate([
           'date' => 'required|date',
           'pnr' => 'required|unique:daily_sale_tickets',
           'system' => 'required',
           'issued_price' => 'nullable|numeric',
           'pax_name' => 'required',
           'sale_price' => 'required|numeric',
           'payment' => 'required|numeric',
           'payment_stage' => 'required|in:first,second,remaining',
           'type' => 'required|in:cash,card,bank',
           'card_name' => 'nullable|string',
           'time_limit' => 'required|date',
           'ticket_number' => 'nullable',
           'remarks' => 'required',
       ]);
    $data['issued_price'] = $request->issued_price ?? 0;
    $pending = (float) $data['sale_price'] - (float) $data['payment'];
    $data['pendings'] = $pending;
    $data['user_id'] = Auth::id();
    $data['status'] = 'booked';
       $data['payment_method']=$request->payment_stage;
    // ✅ Correct way: Parse datetime-local as Spain timezone, then convert to UTC
    $spainTime = \Carbon\Carbon::createFromFormat(
        'Y-m-d\TH:i',
        $request->time_limit,
        'Europe/Madrid'
    );

    $data['time_limit'] = $spainTime->clone()->timezone('UTC'); // Store in DB as UTC

    $ticket=DailySaleTicket::create($data);
    // ✅ 2. Insert Payment Record
    DB::table('daily_sale_ticket_payments')->insert([
        'daily_sale_ticket_id' => $ticket->id,
        'amount' => $request->payment,
        'payment_date' => $request->date,
        'payment_method' => ucfirst($request->type), // Cash / Card / Bank
        'card_name' => $request->type === 'card' ? $request->card_name : null,
        'remarks' => $request->remarks,
        'payment_stage' => $request->payment_stage,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    // 📝 Activity Log
    $this->logActivity('ticket_add', 'New ticket added — PNR: ' . $request->pnr . ', Pax: ' . $request->pax_name . ', Sale: ' . $request->sale_price);
    toastr()->success('Ticket created successfully.');
    return redirect()->route('daily_report.index')->with('success', 'Ticket created successfully.');
}


public function edit($id)
{
    $ticket = DailySaleTicket::findOrFail($id);
    // Spain timezone me convert
    if ($ticket->time_limit) {
        $ticket->time_limit = $ticket->time_limit->copy()->timezone('Europe/Madrid');
    }
    // dd($ticket->time_limit);
    $payment = DB::table('daily_sale_ticket_payments')
        ->where('daily_sale_ticket_id', $id)
        ->orderBy('id', 'desc') // latest payment
        ->first();
    return view('admin.daily_report.edit', compact('ticket','payment'));
}



public function updateold(Request $request, $id)
{
    $request->validate([
        'date' => 'required|date',
        'pnr' => 'required|unique:daily_sale_tickets,pnr,' . $id,
        'system' => 'required',
        'issued_price' => 'required|numeric',
        'pax_name' => 'required',
        'sale_price' => 'required|numeric',
        'payment' => 'required|numeric',
        'payment_method' => 'required',
        'time_limit' => 'required|date',
        'ticket_number' => 'nullable',
        'remarks' => 'required',
        'type' => 'nullable'
    ]);

    $ticket = DailySaleTicket::findOrFail($id);

    $pending = (float) $request->sale_price - (float) $request->payment;

    // Convert Spain time to UTC before update
    $spainTime = \Carbon\Carbon::parse($request->time_limit, 'Europe/Madrid');
    $utcTime = $spainTime->timezone('UTC');

    $ticket->update([
        'date' => $request->date,
        'pnr' => $request->pnr,
        'system' => $request->system,
        'issued_price' => $request->issued_price,
        'pax_name' => $request->pax_name,
        'sale_price' => $request->sale_price,
        'payment' => $request->payment,
        'payment_method' => $request->payment_method,
        'time_limit' => $utcTime,
        'ticket_number' => $request->ticket_number,
        'remarks' => $request->remarks,
        'pendings' => $pending,
        'type' => $request->type
    ]);

    toastr()->success('Ticket updated successfully.');
    return redirect()->route('daily_report.index')->with('success', 'Ticket updated successfully.');
}
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'pnr' => 'required|unique:daily_sale_tickets,pnr,' . $id,
            'system' => 'required',
            'issued_price' => 'nullable|numeric',
            'pax_name' => 'required',
            'sale_price' => 'required|numeric',
            'payment' => 'required|numeric',
            'payment_stage' => 'required|in:first,second,remaining',
            'type' => 'required|in:cash,card,bank',
            'card_name' => 'nullable|string',
            'time_limit' => 'required|date',
            'ticket_number' => 'nullable',
            'remarks' => 'required',
        ]);

        DB::beginTransaction();

        try {

            $ticket = DailySaleTicket::findOrFail($id);

            $pending = (float)$request->sale_price - (float)$request->payment;

            $spainTime = \Carbon\Carbon::createFromFormat(
                'Y-m-d\TH:i',
                $request->time_limit,
                'Europe/Madrid'
            );
//dd($request->ticket_number);
            // ✅ Update ticket
            $ticket->update([
                'date' => $request->date,
                'pnr' => $request->pnr,
                'system' => $request->system,
                'issued_price' => $request->issued_price ?? 0,
                'pax_name' => $request->pax_name,
                'sale_price' => $request->sale_price,
                'payment' => $request->payment,
                'pendings' => $pending,
                'time_limit' => $spainTime->timezone('UTC'),
                'ticket_number' => $request->ticket_number,
                'remarks' => $request->remarks,
                'type' => $request->type,
                'card_name' => $request->type === 'card' ? $request->card_name : null,
                'payment_method' => $request->payment_stage,
            ]);

            // ✅ Payment table sync
            $payment = DB::table('daily_sale_ticket_payments')
                ->where('daily_sale_ticket_id', $ticket->id)
                ->where('payment_stage', $request->payment_stage)
                ->first();

            if ($payment) {
                DB::table('daily_sale_ticket_payments')
                    ->where('id', $payment->id)
                    ->update([
                        'amount' => $request->payment,
                        'payment_method' => ucfirst($request->type),
                        'card_name' => $request->type === 'card' ? $request->card_name : null,
                        'remarks' => $request->remarks,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('daily_sale_ticket_payments')->insert([
                    'daily_sale_ticket_id' => $ticket->id,
                    'amount' => $request->payment,
                    'payment_date' => $request->date,
                    'payment_method' => ucfirst($request->type),
                    'card_name' => $request->type === 'card' ? $request->card_name : null,
                    'remarks' => $request->remarks,
                    'payment_stage' => $request->payment_stage,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('daily_report.index')
                ->with('success', 'Updated Successfully');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


 public function show($id)
    {
        $ticket  = DailySaleTicket::findOrFail($id);
        return view('admin.daily_report.invoice', compact('ticket'));
    }
    public function destroy($id)
    {
        DailySaleTicket::findOrFail($id)->delete();
        toastr()->success('Ticket deleted successfully.');
        return redirect()->route('daily_report.index')->with('success', 'Ticket deleted successfully.');
    }
public function addTicket(Request $request)
{
    $request->validate([
        'id' => 'required|integer',
        'ticket_number' => 'required|numeric|min:1',
    ]);

    $ticket = DailySaleTicket::findOrFail($request->id);
    $ticket->ticket_number = (int) $request->ticket_number;
    $ticket->save();

    return response()->json([
        'success' => true,
        'message' => 'Ticket number added successfully'
    ]);
}


    public function exportTicketExcel(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date, 'Europe/Madrid')->startOfDay() : Carbon::now('Europe/Madrid')->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date, 'Europe/Madrid')->endOfDay() : Carbon::now('Europe/Madrid')->endOfDay();

        $tickets = DailySaleTicket::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        $printingTime = Carbon::now('Europe/Madrid')->format('d/m/Y h:i:s A');
        $filename = 'SanguTravel_Report_' . now()->format('Y-m-d_His') . '.xls';

        // --- XML Excel Header ---
        $xml = '<?xml version="1.0"?>
    <?mso-application progid="Excel.Sheet"?>
    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
              xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
        <Styles>
            <Style ss:ID="header">
                <Font ss:Bold="1" ss:Size="18" ss:Color="#1F4E78"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="subHeader">
                <Font ss:Size="12" ss:Color="#44546A"/>
                <Alignment ss:Horizontal="Center"/>
            </Style>
            <Style ss:ID="metaHeader">
                <Font ss:Bold="1" ss:Size="11"/>
                <Alignment ss:Horizontal="Right"/>
            </Style>
            <Style ss:ID="tableHeader">
                <Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="12"/>
                <Interior ss:Color="#1F4E78" ss:Pattern="Solid"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
                <Borders>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
            </Style>
            <Style ss:ID="cell">
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
                <Borders>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
            </Style>
            <Style ss:ID="cellAlt">
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
                <Interior ss:Color="#F2F2F2" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
            </Style>
            <Style ss:ID="total">
                <Font ss:Bold="1" ss:Size="12" ss:Color="#FFFFFF"/>
                <Interior ss:Color="#2E75B6" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                </Borders>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
        </Styles>

        <Worksheet ss:Name="Daily Sale Tickets">
        <Table>
            <Column ss:Width="60"/>
            <Column ss:Width="90"/>
            <Column ss:Width="120"/>
            <Column ss:Width="80"/>
            <Column ss:Width="80"/>
            <Column ss:Width="80"/>
            <Column ss:Width="80"/>
            <Column ss:Width="80"/>
            <Column ss:Width="80"/>
            <Column ss:Width="100"/>
            <Column ss:Width="80"/>';

        // --- HEADER SECTION ---
        $xml .= '
        <Row ss:Height="25"><Cell ss:MergeAcross="10" ss:StyleID="header"><Data ss:Type="String">Sangu Travel &amp; Tours</Data></Cell></Row>
        <Row><Cell ss:MergeAcross="10" ss:StyleID="subHeader"><Data ss:Type="String">Adventure Never Ends</Data></Cell></Row>
        <Row><Cell ss:MergeAcross="10" ss:StyleID="subHeader"><Data ss:Type="String">Email: info@sangupk.com | Web: www.sangupk.com</Data></Cell></Row>
        <Row><Cell ss:MergeAcross="10" ss:StyleID="subHeader"><Data ss:Type="String">Daily Sale Tickets Report</Data></Cell></Row>
        <Row><Cell ss:MergeAcross="10" ss:StyleID="subHeader"><Data ss:Type="String">From: '.$startDate->format('d/m/Y').' To: '.$endDate->format('d/m/Y').'</Data></Cell></Row>
        <Row><Cell ss:MergeAcross="10" ss:StyleID="metaHeader"><Data ss:Type="String">Printing Date: '.$printingTime.'</Data></Cell></Row>
        <Row/>'; // blank line

        // --- TABLE HEADERS ---
        $xml .= '
        <Row ss:Height="20">
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Invoice ID</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Invoice Date</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">First Passenger</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">PNR</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">System</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Issued</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Sale</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Pending</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Payment</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Added By</Data></Cell>
            <Cell ss:StyleID="tableHeader"><Data ss:Type="String">Status</Data></Cell>
        </Row>';

        $totalIssued = $totalSale = $totalPending = $totalPayment = 0;
        $idx = 0;

        foreach ($tickets as $t) {
            $style = ($idx % 2 == 0) ? 'cell' : 'cellAlt';
            $user = User::find($t->user_id);
            $addedBy = $user ? $user->firstName . ' ' . $user->lastName : 'none';
            $invoiceDate = $t->date ? Carbon::parse($t->date)->format('M d, Y') : '';

            $xml .= '
        <Row>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="Number">'.$t->id.'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.$invoiceDate.'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.e($t->pax_name).'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.e($t->pnr).'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.e($t->system).'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="Number">'.number_format($t->issued_price, 2, '.', '').'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="Number">'.number_format($t->sale_price, 2, '.', '').'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="Number">'.number_format($t->pendings, 2, '.', '').'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="Number">'.number_format($t->payment, 2, '.', '').'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.e($addedBy).'</Data></Cell>
            <Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.ucfirst($t->status).'</Data></Cell>
        </Row>';
            $totalIssued += $t->issued_price;
            $totalSale += $t->sale_price;
            $totalPending += $t->pendings;
            $totalPayment += $t->payment;
            $idx++;
        }

        // --- TOTAL ROW ---
        $xml .= '
        <Row ss:Height="20">
            <Cell ss:MergeAcross="4" ss:StyleID="total"><Data ss:Type="String">Grand Total</Data></Cell>
            <Cell ss:StyleID="total"><Data ss:Type="Number">'.number_format($totalIssued, 2, '.', '').'</Data></Cell>
            <Cell ss:StyleID="total"><Data ss:Type="Number">'.number_format($totalSale, 2, '.', '').'</Data></Cell>
            <Cell ss:StyleID="total"><Data ss:Type="Number">'.number_format($totalPending, 2, '.', '').'</Data></Cell>
            <Cell ss:StyleID="total"><Data ss:Type="Number">'.number_format($totalPayment, 2, '.', '').'</Data></Cell>
            <Cell ss:MergeAcross="1" ss:StyleID="total"/>
        </Row>
        <Row/>';

        // --- FOOTER ---
        $xml .= '
        </Table>
        </Worksheet>
    </Workbook>';

        return response($xml)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Cache-Control', 'max-age=0');
    }

    public function exportTicketPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date, 'Europe/Madrid')->startOfDay() : Carbon::now('Europe/Madrid')->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date, 'Europe/Madrid')->endOfDay() : Carbon::now('Europe/Madrid')->endOfDay();

        $tickets = DailySaleTicket::whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        $printingTime = Carbon::now('Europe/Madrid')->format('d/m/Y h:i:s A');
        $filename = 'SanguTravel_Report_' . now()->format('Y-m-d_His') . '.pdf';

        $totalIssued = $tickets->sum('issued_price');
        $totalSale = $tickets->sum('sale_price');
        $totalPending = $tickets->sum('pendings');
        $totalPayment = $tickets->sum('payment');

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sangu Travel & Tours - Daily Sale Tickets</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1F4E78; font-size: 24px; }
        .header p { margin: 2px 0; color: #44546A; font-size: 12px; }
        .meta { text-align: right; margin-bottom: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th { background-color: #1F4E78; color: white; padding: 8px; text-align: center; font-size: 12px; }
        td { padding: 6px; text-align: center; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .total-row { background-color: #2E75B6 !important; font-weight: bold; }
        .total-row td { color: white; font-size: 12px;}
    </style>
</head>
<body>
    <div class="header">
        <h1>Sangu Travel &amp; Tours</h1>
        <p>Adventure Never Ends</p>
        <p>Email: info@sangupk.com | Web: www.sangupk.com</p>
        <p><strong>Daily Sale Tickets Report</strong></p>
        <p>From: ' . $startDate->format('d/m/Y') . ' To: ' . $endDate->format('d/m/Y') . '</p>
    </div>
    <div class="meta">
        Printing Date: ' . $printingTime . '
    </div>
    <table>
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Invoice Date</th>
                <th>Passenger</th>
                <th>PNR</th>
                <th>System</th>
                <th>Issued</th>
                <th>Sale</th>
                <th>Pending</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

        foreach($tickets as $t) {
            $invoiceDate = $t->date ? Carbon::parse($t->date)->format('M d, Y') : '';
            $html .= '<tr>
                <td>'.$t->id.'</td>
                <td>'.$invoiceDate.'</td>
                <td>'.e($t->pax_name).'</td>
                <td>'.e($t->pnr).'</td>
                <td>'.e($t->system).'</td>
                <td>'.number_format($t->issued_price, 2).'</td>
                <td>'.number_format($t->sale_price, 2).'</td>
                <td>'.number_format($t->pendings, 2).'</td>
                <td>'.number_format($t->payment, 2).'</td>
                <td>'.ucfirst($t->status).'</td>
            </tr>';
        }

        $html .= '<tr class="total-row">
            <td colspan="5" style="text-align:right; padding-right:10px;">Grand Total</td>
            <td>'.number_format($totalIssued, 2).'</td>
            <td>'.number_format($totalSale, 2).'</td>
            <td>'.number_format($totalPending, 2).'</td>
            <td>'.number_format($totalPayment, 2).'</td>
            <td></td>
        </tr>
        </tbody>
    </table>
</body>
</html>';

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($filename);
    }

    public function storePayment(Request $request)
    {
        $ticket = DailySaleTicket::findOrFail($request->ticket_id);

        DailySaleTicketPayment::create([
            'daily_sale_ticket_id' => $request->ticket_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
            'payment_stage' => 'remaining',
        ]);

        // Update Pending
        $ticket->pendings = $ticket->pendings - $request->amount;
        $ticket->save();

        return response()->json(['success'=>true]);
    }
    public function history($id)
    {
        $payments = DailySaleTicketPayment::where('daily_sale_ticket_id',$id)->get();

        $html = '<table class="table table-bordered">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Payment Stage</th>
                        <th>Remarks</th>
                    </tr>';

        foreach($payments as $key => $pay){
            $html .= '<tr>
                        <td>'.($key+1).'</td>
                        <td>'.$pay->payment_date.'</td>
                        <td>'.$pay->amount.'</td>
                        <td>'.$pay->payment_method.'</td>
                        <td>'.$pay->payment_stage.'</td>
                        <td>'.$pay->remarks.'</td>
                      </tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public function updateStatus(Request $request)
    {
//        dd($request->all());

        $request->validate([
            'id' => 'required|exists:daily_sale_tickets,id',
            'status' => 'required|in:issue,cancel,booked,reissue'
        ]);

        $ticket = DailySaleTicket::findOrFail($request->id);
        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        $ticket->save();

        // 📝 Activity Log
        $this->logActivity('status_change', 'Ticket #' . $ticket->id . ' (PNR: ' . $ticket->pnr . ') status changed from ' . strtoupper($oldStatus) . ' → ' . strtoupper($request->status));

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
    public function getTicketDetails($id)
    {
        try {
            // Load the ticket with payments relationship
            $ticket = DailySaleTicket::with('payments')->find($id);

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Format the response data
            $responseData = [
                'id' => $ticket->id,
                'date' => $ticket->date,
                'time_limit' => $ticket->time_limit,
                'pax_name' => $ticket->pax_name,
                'pnr' => $ticket->pnr,
                'system' => $ticket->system,
                'issued_price' => $ticket->issued_price,
                'sale_price' => $ticket->sale_price,
                'pendings' => $ticket->pendings,
                'added_by' => $ticket->added_by,
                'ticket_number' => $ticket->ticket_number,
                'type' => $ticket->type,
                'remarks' => $ticket->remarks,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $ticket->updated_at ? $ticket->updated_at->format('Y-m-d H:i:s') : null,
                'payments' => []
            ];

            // Add payments if they exist
            if ($ticket->payments && $ticket->payments->count() > 0) {
                foreach ($ticket->payments as $payment) {
                    $responseData['payments'][] = [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'payment_date' => $payment->payment_date,
                        'payment_method' => $payment->payment_method,
                        'card_name' => $payment->card_name, // ✅ Add card name
                        'payment_stage' => $payment->payment_stage, // ✅ Add payment stage
                        'remarks' => $payment->remarks,
                        'created_at' => $payment->created_at ? $payment->created_at->format('Y-m-d H:i:s') : null
                    ];
                }
            }

            return response()->json($responseData);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching ticket details: ' . $e->getMessage()
            ], 500);
        }
    }
}
