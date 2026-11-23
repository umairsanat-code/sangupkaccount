<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Airline;
use App\Models\Customer;
use App\Models\PaymentTicket;
use App\Models\Profit;
use App\Models\Sale;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use Yajra\DataTables\DataTables;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Base query for tickets
            $tickets = Ticket::with(['airline', 'customer']);

            // Apply filters
            if ($request->departure_city) {
                $tickets->where('departure_city', 'LIKE', '%' . $request->departure_city . '%');
            }

            if ($request->airline_id) { // Filter by airline_id
                $tickets->where('airline_id', $request->airline_id);
            }
            if ($request->issue_start_date || $request->issue_end_date) {
                // dd($request->issue_start_date || $request->issue_end_date);

                $tickets->whereBetween('flight_date', [$request->issue_start_date, $request->issue_end_date]);
            }
            //  if ($request->issue_date) {
            //     $tickets->where('flight_date', 'LIKE', '%' . $request->issue_date . '%');
            // }

            if ($request->ticket_number) {
                $tickets->where('ticket_number', 'LIKE', '%' . $request->ticket_number . '%');
            }

            return DataTables::of($tickets)
                ->addColumn('customer', function ($ticket) {
                    return $ticket->customer->name;
                })
                ->addColumn('airline', function ($ticket) {
                    return $ticket->airline->name;
                })
                ->addColumn('actions', function ($ticket) {
                    return '
                    <a href="' . route('tickets-detail', $ticket->id) . '" class="btn btn-primary btn-sm">View</a>
                    <form action="' . route('tickets.destroy', $ticket) . '" method="POST" class="d-inline">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        // Fetch all airlines for the dropdown
        $airlines = Airline::all();

        return view('admin.tickets.index', compact('airlines'));
    }


    public function create()
    {
        $airlines = Airline::all();
        $customers = Customer::all();

        return view('admin.tickets.create', compact('airlines', 'customers'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|unique:tickets',
            'airline_id' => 'required|exists:airlines,id',
            'customer_id' => 'required|exists:customers,id',
            'flight_date' => 'required|date',
            // 'booking_date' => 'required|date',
            'departure_city' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'class' => 'required|in:Economy,Business,First Class',
            'status' => 'required|in:Booked,Cancelled,Checked In,VOIDED',
            'purchase_price' => 'required|numeric',
            'sale_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'payment_status' => 'required|in:Pending,Paid,Partially Paid',
            'amount_paid' => 'nullable|numeric|min:0|required_if:payment_received,1',
            'payment_received' => 'nullable|boolean',
            'payment_method' => 'required_if:payment_received,1|string|in:Cash,Bank,Card',
            'bank_name' => 'nullable|max:255',
            'payment_date' => 'required_if:payment_received,1',
            // 'card_number' => 'nullable|string|max:20',
            'agent_id'=>'required',
            'customer_name'=>'nullable',
             'author_name'=>'nullable',
             'pnr'=>'required',
             'note'=>'nullable',
            'id_card'=>'nullable',
            'passport_no'=>'nullable',

        ]);

        DB::beginTransaction();
        try {
//            dd($validated['agent_id']);
            $validated['author_name'] = Auth::user()->name;

            $ticket = Ticket::create($validated);
//dd($validated['agent_id']);
            $sale = Sale::create([
                'ticket_id' => $ticket->id,
                'sale_price' => $validated['sale_price'],
                'sale_date' => $validated['sale_date'],
                'agent_id' => $validated['agent_id'], // Dynamically assign later
                'payment_status' => $validated['payment_status'],
                'total_amount_paid' => $validated['amount_paid'] ?? 0,
                'pending_amount' => $validated['sale_price'] - ($validated['amount_paid'] ?? 0),
            ]);
            $profitAmount = $sale->sale_price - $ticket->purchase_price;
            Profit::create([
                'sale_id' => $sale->id,
                'profit_amount' => $profitAmount,
            ]);
//dd($request->payment_received);
            if ($request->payment_received!=null) {
//                dd($request->all());

                PaymentTicket::create([
                    'sale_id' => $sale->id,
                    'amount_paid' => $validated['amount_paid'],
                    'payment_method' => $validated['payment_method'],
                    'bank_name' => $validated['bank_name'],
                    'card_number' => 'none',
                    'payment_date' => $validated['payment_date'],
                ]);

            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ticket created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

//    public function store(Request $request)
//    {
//
//        if (!$request->has('payment_received')) {
//            $request->merge(['payment_received' => 0]);
//        }
//        $validated = $request->validate([
//            'ticket_number' => 'required|unique:tickets',
//            'airline_id' => 'required|exists:airlines,id',
//            'customer_id' => 'required|exists:customers,id',
//            'flight_date' => 'required|date',
//            'booking_date' => 'required|date',
//            'departure_city' => 'required|string|max:255',
//            'destination_city' => 'required|string|max:255',
//            'class' => 'required|in:Economy,Business,First Class',
//            'status' => 'required|in:Booked,Cancelled,Checked In',
//            'purchase_price' => 'required|numeric|min:0',
//            'sale_price' => 'required|numeric|min:0', // Added sale price validation
//            'sale_date' => 'required|date',
//            'payment_status' => 'required|in:Pending,Paid,Partially Paid',
//            // Conditional validation rules
//            'amount_paid' => 'nullable|numeric|min:0|required_if:payment_received,1',
//            'payment_received' => 'required|boolean',
//            'payment_method' => 'required_if:payment_received,1|string|in:Cash,Bank,Card',
//            'payment_date' => 'required_if:payment_received,1',
//            'bank_name' => 'nullable|max:255',
//            'card_number' => 'nullable|string|max:20',
//        ]);
////dd($validated);
//        DB::beginTransaction();
//        try {
//            $customer = Customer::findOrFail($validated['customer_id']);
//            $totalCustomerTickets = $request->total_ticket_customer ?? 1;
////dd($customer);
//            // Create Ticket
//            $ticket = Ticket::create($validated);
////dd($validated['purchase_price'] , $totalCustomerTickets);
//            // Update purchase price based on total tickets
//            $totalPurchasePrice = $validated['purchase_price'] * $totalCustomerTickets;
////            dd($totalPurchasePrice);
//            $ticket->update(['purchase_price' => $totalPurchasePrice]);
//
//            // Calculate Sale Price for all tickets
//            $totalSalePrice = $validated['sale_price'] * $totalCustomerTickets;
//            // Create Sale
//            $sale = Sale::create([
//                'ticket_id' => $ticket->id,
//                'sale_price' => $totalSalePrice,
//                'sale_date' => $validated['sale_date'],
//                'agent_id' => 1, // Dynamically assign later
//                'payment_status' => $validated['payment_status'],
//                'total_amount_paid' => $validated['amount_paid'] ?? 0,
//                'pending_amount' => $totalSalePrice - $validated['amount_paid'] ?? 0,
//            ]);
////dd($sale);
//            // Calculate Profit or Loss
//            $profitAmount = $sale->sale_price - $ticket->purchase_price;
//            Profit::create([
//                'sale_id' => $sale->id,
//                'profit_amount' => $profitAmount,
//            ]);
//
//            // Handle Payment (if any)
//            if ($validated['payment_received']) {
//                PaymentTicket::create([
//                    'sale_id' => $sale->id,
//                    'amount_paid' => $validated['amount_paid'],
//                    'payment_method' => $validated['payment_method'],
//                    'bank_name' => $validated['bank_name'],
//                    'card_number' => $validated['card_number'],
//                    'payment_date' => $validated['payment_date'],
//                ]);
//            }
//
//            DB::commit();
//            return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
//        } catch (\Exception $e) {
//            dd($e->getMessage());
//            DB::rollBack();
//            return back()->withErrors('An error occurred: ' . $e->getMessage());
//        }
//    }

    public function edit($id)
    {
        $ticket = Ticket::with(['sales', 'sales.paymentMethod','airline'])->findOrFail($id);
//        dd($ticket->toArray());
        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    public function update(Request $request, $ticketId)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|unique:tickets,ticket_number,' . $ticketId,
            'airline_id' => 'required|exists:airlines,id',
//            'customer_id' => 'required|exists:customers,id',
            'flight_date' => 'required|date',
            // 'booking_date' => 'required|date',
            'departure_city' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'class' => 'required|in:Economy,Business,First Class',
            'status' => 'required|in:Booked,Cancelled,Checked In,VOIDED',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'payment_status' => 'required|in:Pending,Paid,Partially Paid',
            'amount_paid' => 'nullable|numeric|min:0|required_if:payment_received,1',
            'payment_received' => 'required|boolean',
            'payment_method' => 'required_if:payment_received,1|string|in:Cash,Bank,Card',
            'bank_name' => 'nullable|max:255',
            'payment_date' => 'required_if:payment_received,1',
            // 'card_number' => 'nullable|string|max:20',
            'customer_name'=>'nullable',
            'pnr'=>'nullable',
            'note'=>'nullable',
            'agent_id'=>'nullable',
            'passport_no'=>'nullable',
            'id_card'=>'nullable',

        ]);

        DB::beginTransaction();
        try {
            // Find the ticket to update
            $ticket = Ticket::findOrFail($ticketId);
            $ticket->update($validated);

            // Update the sale record related to this ticket
            $sale = Sale::find($request->sale_id);
//            dd($request->all());

            $sale->update([
                'sale_price' => $validated['sale_price'],
                'sale_date' => $validated['sale_date'],
                'payment_status' => $validated['payment_status'],
                'total_amount_paid' => $validated['amount_paid'] ?? 0,
                'pending_amount' => $validated['sale_price'] - ($validated['amount_paid'] ?? 0),
            ]);
            // Update profit if sale price or purchase price changed
            $profitAmount = $sale->sale_price - $ticket->purchase_price;
            $sale->profit->update([
                'profit_amount' => $profitAmount,
            ]);

            // If payment has been received, update the payment details
            if ($validated['payment_received']) {
                $paymentTicket = $sale->paymentMethod ?? new PaymentTicket();
                $paymentTicket->update([
                    'amount_paid' => $validated['amount_paid'],
                    'payment_method' => $validated['payment_method'],
                    'bank_name' => $validated['bank_name'],
                    'card_number' => 'none',
                    'payment_date' => $validated['payment_date'],
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ticket updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
                toastr()->success('Ticket deleted successfully.');

        return redirect()->back()->with('success', 'Ticket deleted successfully.');
    }
    public function show($id , Request $request)
    {
        return $this->ticketDetail($id ,$request);
    }
    public function ticketDetail($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        $perPage = 10;

        // Active Tickets
        $tickets = $customer->tickets()
            ->where('refund_status', 'Not Refunded')
            ->orderBy('flight_date', 'desc')
            ->paginate($perPage, ['*'], 'active_page');

        // Refunded Tickets
        $refundedTickets = $customer->tickets()
            ->where('refund_status', 'Refunded')
            ->with('sales')
            ->orderBy('flight_date', 'desc')
            ->paginate($perPage, ['*'], 'refunded_page');

        // Totals for Active Tickets
        $totalSalePrice = $tickets->sum(function ($ticket) {
            return $ticket->sales->sum('sale_price');
        });
        $totalPurchasePrice = $tickets->sum('purchase_price');
        $totalProfit = $totalSalePrice - $totalPurchasePrice;
        $totalPendingAmount = $tickets->sum(function ($ticket) {
            return $ticket->sales->sum('pending_amount');
        });

        // Refunded Amount
        $refundedAMount = $refundedTickets->sum(function ($ticket) {
            return $ticket->sales->sum('refund_amount');
        });

        $airlines = Airline::all();
        $agents = Agent::all();

        return view('admin.tickets.ticket-detail', compact(
            'customer',
            'tickets',
            'refundedTickets',
            'totalSalePrice',
            'totalPurchasePrice',
            'totalProfit',
            'totalPendingAmount',
            'refundedAMount',
            'airlines',
            'agents'
        ));
    }
    public function ticketActiveAjax($id, Request $request)
    {
        $customer = Customer::findOrFail($id);

        $tickets = $customer->tickets()
            ->where('refund_status', 'Not Refunded')
            ->orderBy('flight_date', 'desc')
            ->paginate(10, ['*'], 'active_page');

        // ✅ Use Bootstrap pagination view for proper styling
        $html = view('admin.tickets.partials.active_ajax', compact('tickets', 'customer'))->render();
        $pagination = $tickets->appends(request()->except('active_page'))
            ->onEachSide(1)
            ->links('vendor.pagination.bootstrap-4')
            ->render();

        return response()->json([
            'html' => $html,
            'pagination' => $pagination,
        ]);
    }


    public function ticketRefundedAjax($id, Request $request)
    {
        $customer = Customer::findOrFail($id);

        $refundedTickets = $customer->tickets()
            ->where('refund_status', 'Refunded')
            ->with('sales')
            ->orderBy('flight_date', 'desc')
            ->paginate(10, ['*'], 'refunded_page');

        // Render the refunded tickets partial
        $html = view('admin.tickets.partials.refunded_ajax', compact('refundedTickets', 'customer'))->render();

        // Use Bootstrap pagination style (4 or 5 depending on what you have)
        $pagination = $refundedTickets
            ->appends(request()->except('refunded_page'))
            ->onEachSide(1)
            ->links('vendor.pagination.bootstrap-4') // 👈 use bootstrap-4 if bootstrap-5 not found
            ->render();

        return response()->json([
            'html' => $html,
            'pagination' => $pagination
        ]);
    }

    public function activateRefundedTicket($id)
    {
        $ticket = Ticket::with('sales')->findOrFail($id);

        // Ticket status update
        $ticket->update([
            'refund_status' => 'Not Refunded',
            'status'        => 'Booked',
        ]);

        // Agar sale level pe refund amount hai to reset
//        foreach ($ticket->sales as $sale) {
//            $sale->update([
//                'refund_amount' => 0
//            ]);
//        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket successfully re-activated'
        ]);
    }

    public function clearPayment(Request $request)
    {
        // Validate the input
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'ticket_id' => 'required|exists:tickets,id',
            'payment_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,bank,card',
            'payment_note' => 'nullable|string',
        ]);
//dd($request->all());
        $ticketId = $request->ticket_id;
        $customerId = $request->customer_id;
        $paymentAmount = $request->payment_amount;
        $paymentType = $request->payment_type;
        $notes = $request->payment_note;

        // Retrieve the specific ticket's sales with pending payments
        $sales = Sale::where('ticket_id', $ticketId)
            ->where('pending_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingAmount = $paymentAmount;

        foreach ($sales as $sale) {
            if ($remainingAmount <= 0) break;

            $pendingAmount = $sale->pending_amount;

            $paymentToApply = min($remainingAmount, $pendingAmount);

            // Record the payment in the PaymentTicket table
            PaymentTicket::create([
                'sale_id' => $sale->id,
                'ticket_id' => $ticketId,
                'customer_id' => $customerId,
                'amount_paid' => $paymentToApply,
                'payment_method' => $paymentType,
                'payment_date' => $request->payment_date,
                'note' => $notes ,
            ]);

            // Update the sale's payment details
            $sale->total_amount_paid += $paymentToApply;
            $sale->pending_amount -= $paymentToApply;
            $sale->save();

            $remainingAmount -= $paymentToApply;
        }

        return redirect()->back()->with('success', 'Payment successfully cleared!');
    }

    public function processRefund(Request $request)
    {
        $ticket = Ticket::findOrFail($request->ticket_id);
        $sale = $ticket->sales()->where('id', $request->sale_id)->first();

        // Check if the sale exists
        if (!$sale) {
            return redirect()->back()->with('error', 'Sale not found for this ticket.');
        }

        // Refund amount after subtracting service and airline charges
        $refundAmount = $request->refund_amount - $request->service_charges - $request->airline_charges;

        // Update the sale record with refund details
        $sale->update([
            'refund_amount' => $request->refund_amount,
            'refund_reason' => $request->refund_reason,
            'service_charges' => $request->service_charges,
            'airline_charges' => $request->airline_charges,
        ]);

        // Update ticket's refund status
        $ticket->update([
            'refund_status' => 'Refunded',
        ]);

        // Update the pending amount in the sale record (if applicable)
        $pendingAmount = $sale->pending_amount - $refundAmount;
        $sale->update([
            'pending_amount' => max($pendingAmount, 0),  // Prevent negative pending amounts
        ]);

        // Calculate the adjusted profit after refund
        $newProfit = ($sale->sale_price - $refundAmount) - $ticket->purchase_price;

        // Update or create the profit entry
        $profit = Profit::where('sale_id', $sale->id)->first();

        if ($profit) {
            // If profit record exists, update it
            $profit->update([
                'profit_amount' => $newProfit,
            ]);

        } else {
            // If profit record doesn't exist, create a new one
            Profit::create([
                'sale_id' => $sale->id,
                'profit_amount' => $newProfit,
            ]);
        }

        // Optionally, handle any other logic like returning the remaining refund amount to the customer
        return back()->withErrors(['error' => 'Service charges and airline charges cannot exceed the refund amount.']);

//        return redirect()->back()->with('success', 'Refund processed successfully and profit adjusted.');
    }
    public function paymentHistory(Request $request, $id)
    {
        $ticket = Ticket::with('sales.payments')->find($id);

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket not found']);
        }

        // Calculate total paid and pending amounts
        $totalPaid = $ticket->sales->sum(function ($sale) {
            return $sale->payments->sum('amount_paid');
        });

        $totalPending = $ticket->sales->sum('pending_amount');

        // Gather payment history with pagination
        $allPayments = $ticket->sales->flatMap(function ($sale) {
            return $sale->payments->map(function ($payment) {
                return [
                    'amount' => $payment->amount_paid,
                    'date' => $payment->payment_date,
                    'method' => ucfirst($payment->payment_method),
                    'notes' => $payment->note ?? 'N/A',
                ];
            });
        });

        // Paginate the payments
        $perPage = 5; // Adjust the number of records per page
        $page = $request->get('page', 1); // Get the current page from the request
        $paginatedPayments = $allPayments->forPage($page, $perPage);

        return response()->json([
            'success' => true,
            'ticket_number' => $ticket->ticket_number,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'payments' => $paginatedPayments->values(),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $allPayments->count(),
            ],
        ]);
    }
    public function generateInvoice($customerId)
    {
        // Fetch the customer by ID
        $customer = Customer::find($customerId);
//dd($customer);
        if (!$customer) {
            abort(404, 'Customer not found.');
        }

        // Fetch all tickets related to the customer
        $tickets = Ticket::with(['sales.payments'])
            ->where('customer_id', $customerId)
            ->get();

        // Calculate total paid and total pending
        $totalPaid = 0;
        $totalPending = 0;

        // Prepare invoices with ticket, paid and pending amounts
        $invoices = $tickets->map(function ($ticket) use (&$totalPaid, &$totalPending) {
            $paid = 0;
            $pending = 0;

            // Sum up payments for each ticket
            foreach ($ticket->sales as $sale) {
                $paid += $sale->payments->sum('amount_paid');
                $pending += $sale->pending_amount;
            }

            // Add to the overall totals
            $totalPaid += $paid;
            $totalPending += $pending;

            return [
                'ticket' => $ticket,
                'totalPaid' => $paid,
                'totalPending' => $pending,
            ];
        });

        // Pass the data to the view
        return view('admin.tickets.invoice', [
            'customer' => $customer,
            'invoices' => $invoices,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
        ]);

    }
    public function finalgenerateInvoice($customerId)
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            abort(404, 'Customer not found.');
        }

        // Load all tickets + sales + payment info
        $tickets = Ticket::with(['sales.payments'])
            ->where('customer_id', $customerId)
            ->get();

        $totalPaid = 0;
        $totalPending = 0;

        $invoices = $tickets->map(function ($ticket) use (&$totalPaid, &$totalPending) {

            $paid = 0;
            $pending = 0;
            $paymentType = 'N/A';

            // Loop all sales of this ticket
            foreach ($ticket->sales as $sale) {

                // Total paid/pending
                $paid += $sale->payments->sum('amount_paid');
                $pending += $sale->pending_amount;

                // Get last payment of sale
                if ($sale->payments->isNotEmpty()) {

                    $payment = $sale->payments->last();

                    $method     = $payment->payment_method;
                    $bankName   = $payment->bank_name;
                    $cardNumber = $payment->card_number;

                    // Build payment type
                    if ($method === 'Cash') {
                        $paymentType = 'Paid with Cash';

                    } elseif ($method === 'Bank') {
                        $paymentType = 'Paid in Bank';
                        if ($bankName) {
                            $paymentType .= " ($bankName)";
                        }

                    } elseif ($method === 'Card') {
                        $paymentType = 'Paid with Card';
                        if ($cardNumber) {
                            $paymentType .= ' (XXXX-' . substr($cardNumber, -4) . ')';
                        }

                    } else {
                        $paymentType = $method;
                    }
                }
            }

            // Add totals
            $totalPaid += $paid;
            $totalPending += $pending;

            return [
                'ticket'        => $ticket,
                'px_name'       => $ticket->customer_name ?? 'N/A',
                'ticket_no'     => $ticket->ticket_number ?? 'N/A',
                'payment_type'  => $paymentType,
                'totalPaid'     => $paid,
                'totalPending'  => $pending,
            ];
        });

        return view('admin.tickets.final_invoice', [
            'customer'     => $customer,
            'invoices'     => $invoices,
            'totalPaid'    => $totalPaid,
            'totalPending' => $totalPending,
        ]);
    }

//    use Barryvdh\DomPDF\Facade\Pdf;

   public function downloadPDF($id)
{
    // Debugging purpose
    // dd('downloadpdf');

    // Fetch customer data
    $customer = Customer::find($id);
    if (!$customer) {
        return redirect()->back()->withErrors(['error' => 'Customer not found']);
    }

    // Fetch tickets with related sales and payments
    $tickets = Ticket::with(['sales.payments'])->where('customer_id', $id)->get();

    // Calculate totals and other data
    $totalPaid = $tickets->sum(fn($ticket) => $ticket->sales->sum(fn($sale) => $sale->payments->sum('amount_paid')));
    $totalPending = $tickets->sum('amount') - $totalPaid;

    $invoices = $tickets->map(function ($ticket) {
        $paid = $ticket->sales->sum(fn($sale) => $sale->payments->sum('amount_paid'));
        $pending = $ticket->sales->sum('pending_amount');

        return [
            'ticket' => $ticket,
            'totalPaid' => $paid,
            'totalPending' => $pending,
        ];
    });

    // Generate PDF using the provided data
    $pdf = Pdf::loadView('admin.tickets.final_invoice', [
        'customer' => $customer,
        'tickets' => $tickets,
        'invoices' => $invoices,
        'totalPaid' => $totalPaid,
        'totalPending' => $totalPending,
    ]);

    // Use a cross-platform-compatible file path
    $filePath = public_path('img/invoice.pdf');
    $pdf->save($filePath);

    // Return the file for download and delete it after sending
    return response()->download($filePath, 'invoice.pdf')->deleteFileAfterSend(true);
}







}
