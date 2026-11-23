<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Airline;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\Sale;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Agent::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
               ->addColumn('action', function($row){
                $authUserId = auth()->id(); // Logged-in user ki ID lein

                $btn = '';

                // Sirf user ID 1 ke liye Edit aur Delete buttons dikhayen
                if ($authUserId == 1) {
                    $btn .= '<button type="button" data-id="'.$row->id.'" class="edit btn btn-success btn-sm">Edit</button>';
                    $btn .= '<button type="button" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
    }

    // View Tickets button sab users ke liye show hoga
    $btn .= '<button type="button" data-id="'.$row->id.'" class="view-details btn btn-info btn-sm">View Tickets</button>';

    return $btn;
})

                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.agents.index');
    }

    // Show the form to create a new agent
    public function create()
    {
        return view('admin.agents.create');
    }

    // Store a new agent
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:agents,name',
            'contact_number' => 'nullable',
            'email' => 'nullable|email',
        ]);

        $agent = Agent::create($request->all());

        return response()->json(['success' => 'Agent created successfully.']);
    }

    // Show the form to edit an agent
    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        return response()->json($agent);
    }

    // Update an agent
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:agents,name,' . $id,
            'contact_number' => 'nullable',
            'email' => 'nullable|email',
        ]);

        $agent = Agent::findOrFail($id);
        $agent->update($request->all());

        return response()->json(['success' => 'Agent updated successfully.']);
    }

    // Delete an agent
    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();

        return response()->json(['success' => 'Agent deleted successfully.']);
    }
    public function fetchTickets(Request $request)
    {
        $agentId = $request->agent_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $tickets = Ticket::where('agent_id', $agentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return response()->json(['tickets' => $tickets]);
    }
//    public function showAgentTickets(Request $request)
//    {
//        $request->validate([
//            'agent_id' => 'required|exists:agents,id',
//            'start_date' => 'required|date',
//            'end_date' => 'required|date|after_or_equal:start_date',
//        ]);
//        $tickets = Ticket::where('agent_id', $request->agent_id)
//            ->whereBetween('created_at', [$request->start_date, $request->end_date])
//            ->get();
//
//        $agent = Agent::findOrFail($request->agent_id);
//
//        return view('admin.agents.tickets', compact('tickets', 'agent'));
//    }
          public function showAgentTickets(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $agent = Agent::findOrFail($request->agent_id);
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // Tickets query (without ->get())
        $tickets = Ticket::where('agent_id', $agent->id)->where('refund_status','Not Refunded')
            ->whereHas('sales', function ($query) use ($start_date, $end_date) {
                $query->whereBetween('sale_date', [$start_date, $end_date]);
            })
            ->with(['sales' => function ($query) use ($start_date, $end_date) {
                $query->whereBetween('sale_date', [$start_date, $end_date]);
            }]);

        // ✅ If Ajax request then return DataTables JSON

        // Agar normal page load hai to totals calculate karen
        $ticketsCollection = $tickets->get();
        $totalIssuePrice = $ticketsCollection->sum('purchase_price');

        $payments = Vendor::where('agent_id', $agent->id)
            ->whereBetween('start_date', [$start_date, $end_date])
            ->get();

        $paidAmount = $payments->sum('payment_amount');
        $remainingAmount = $totalIssuePrice - $paidAmount;

        return view('admin.agents.tickets', compact(
            'start_date',
            'end_date',
            'agent',
            'totalIssuePrice',
            'paidAmount',
            'remainingAmount'
        ));
    }


   public function show(Request $request)
{
    $request->validate([
        'agent_id'   => 'required|exists:agents,id',
        'start_date' => 'nullable|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
    ]);

    $agentId   = $request->agent_id;
    $startDate = $request->start_date;
    $endDate   = $request->end_date;

    // ✅ Base query
    $tickets = Ticket::where('agent_id', $agentId)
        ->whereHas('sales', function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('sale_date', [$startDate, $endDate]);
            }
        })
        ->with([
            'sales'    => function ($query) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $query->whereBetween('sale_date', [$startDate, $endDate]);
                }
            },
            'airline:id,name',
            'customer:id,name',
        ])
        ->select('id', 'ticket_number', 'airline_id', 'customer_id', 'customer_name', 'pnr', 'purchase_price');

    // ✅ DataTables response
    return datatables()->of($tickets)
        ->addColumn('airline', function ($ticket) {
            return $ticket->airline ? $ticket->airline->name : '-';
        })
        ->addColumn('customer_name', function ($ticket) {
            return $ticket->customer_name ?? ($ticket->customer ? $ticket->customer->name : '-');
        })
        ->addColumn('sale_date', function ($ticket) {
            $sale = $ticket->sales->first(); // because relation "with" ho raha hai
            return $sale ? \Carbon\Carbon::parse($sale->sale_date)->format('d-m-Y') : '-';
        })
        ->addColumn('action', function ($ticket) {
            return '<button class="btn btn-info btn-sm">View</button>';
        })
        ->rawColumns(['action'])
        ->make(true);
}





}
