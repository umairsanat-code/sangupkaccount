<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Ticket;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Vendor::query();

            // Add filters if necessary
            // Filter by agent_id (if present in the request)
            if ($request->has('agent_id') && !empty($request->agent_id)) {
                $query->where('agent_id', $request->agent_id);
            }

            // Filter by start_date (if present in the request)
            if ($request->has('start_date') && !empty($request->start_date)) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }

            // Filter by end_date (if present in the request)
            if ($request->has('end_date') && !empty($request->end_date)) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }

            // Fetch the data
            $data = $query->latest()->get();

            // Return the data to DataTables in the correct format
            return DataTables::of($data)
                ->addIndexColumn()  // Adds the index column automatically
                ->addColumn('agent_name', function ($row) {
                    $agent = Agent::where('id', $row->agent_id)->first();
                    return $agent ? $agent->name : 'N/A'; // Return agent name or 'N/A' if not found
                })
              ->addColumn('action', function ($row) {
                       $btn =null;
                  if (Auth::id()==1) {
                      $btn = '<a href="' . route('vendors.payment.edit', $row->id) . '"
            class="btn btn-primary hover:bg-blue-600 text-white px-3 py-2 rounded">
            Edit
        </a>';
                  }
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action', 'agent_name']) // Allows HTML for the action column
                ->make(true);
        }

        // Fetch all agents for the dropdown
        $agents = Agent::all();

        // Return the normal view with agents
        return view('admin.vendors.index', compact('agents'));
    }
    public function calculatePayments($agentId)
    {
        // Get all vendors for a specific agent
        $vendors = Vendor::where('agent_id', $agentId)
            ->whereBetween('start_date', [Carbon::createFromDate(2024, 12, 1), Carbon::createFromDate(2024, 12, 31)])
            ->get();

        // Calculate total payment
        $totalPayment = $vendors->sum('payment_amount');

        // Return to a view with the payment details

        return view('admin.vendors.payment', compact('totalPayment', 'vendors','agentId'));
    }
    public function showPaymentForm()
    {
        // Fetch all agents for the select dropdown
        $agents = Agent::all();
        return view('admin.vendors.create_payment', compact('agents'));
    }



    public function processPayment(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'note'=>'nullable',
               'bank_name'=>'required'

        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the agent's tickets for the specified date range
            // $tickets = Ticket::where('agent_id', $validated['agent_id'])
            //     ->whereBetween('flight_date', [$validated['start_date'], $validated['end_date']])
            //     ->get();
           $tickets =  Ticket::where('agent_id', $validated['agent_id'])
            ->whereHas('sales', function ($query) use ($validated) {
                $query->whereBetween('sale_date',  [$validated['start_date'], $validated['end_date']]);
            })
            ->get();
// dd($tickets,$validated['start_date'], $validated['end_date']);
            // Calculate the total purchase amount for the tickets
            $totalPurchaseAmount = $tickets->sum('purchase_price');

            // Check if there are tickets in the specified range
            if ($tickets->isEmpty()) {
                toastr()->warning('No tickets found for the specified criteria.');
                return back()->with('error', 'No tickets found for the specified criteria.');
            }

            // Retrieve the agent's existing remaining amount from the Vendor table
            $vendor = Vendor::where('agent_id', $validated['agent_id'])
                ->whereBetween('start_date',[$validated['start_date'], $validated['end_date']])->get();
            $remainingAmount=0;
            // Initialize remaining amount
            if ($vendor->count()==0) {

                // If no existing vendor record, treat this as the first payment
                // Debugging to check remaining amount
// $totalPurchaseAmount 800 and $validated['payment_amount'] 700 remaining 100 jb vendor wala table record na ho tu
                if ($validated['payment_amount'] > $totalPurchaseAmount) {
                    // Ensure the remaining amount does not go negative
                    toastr()->warning('Payment amount exceeds the remaining balance of ' . number_format($remainingAmount, 2));
                    return back()->with('error', 'Payment amount exceeds the remaining balance.');

                } else {
                    // If payment amount is within the total purchase amount, calculate the remaining balance
                    $remainingAmount = $totalPurchaseAmount - $validated['payment_amount'];
                 }

            } else {
                // For existing vendor record, subtract the payment amount from the existing remaining amount
                $remainingAmount = $request->remaining_amount - $validated['payment_amount'];
//dd($remainingAmount,$request->all(),408 >  $request->remaining_amount);
                // Ensure the payment does not exceed the remaining amount
                if ($validated['payment_amount'] >  $request->remaining_amount) {

                    toastr()->warning('Payment amount exceeds the remaining balance of ' . number_format($remainingAmount, 2));
                    return back()->with('error', 'Payment amount exceeds the remaining balance.');
                }
            }




            // After the payment, if the remaining amount becomes zero or negative, set it to zero


            // Proceed with the payment and update the remaining amount in the Vendor table
            $vendorPayment = new Vendor();
            $vendorPayment->agent_id = $validated['agent_id'];
            $vendorPayment->ticket_id = 15; // Ensure this is valid (change if necessary)
            $vendorPayment->payment_amount = $validated['payment_amount'];
            $vendorPayment->remaining_amount = $remainingAmount; // Update remaining amount after payment
            $vendorPayment->payment_method = $validated['payment_method'];
            $vendorPayment->start_date = $validated['start_date'];
            $vendorPayment->end_date = $validated['end_date'];
            $vendorPayment->note = $validated['note'];
            $vendorPayment->bank_name = $validated['bank_name'];
            $vendorPayment->save();

            // Commit the transaction
            DB::commit();

            toastr()->success('Payment processed successfully.');
            return redirect()->route('vendors.payment.form')->with('success', 'Payment processed successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
                        toastr()->error($e->getMessage());

            DB::rollBack();
            return back()->with('error', 'An error occurred while processing the payment: ' . $e->getMessage());
        }
    }

    public function getTotalPurchaseAmount(Request $request)
    {
        $agentId = $request->agent_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
//dd($startDate,$endDate);
        // Calculate total purchase amount from tickets
       $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
//dd($startDate,$endDate);
        // Calculate total purchase amount from tickets
        $totalPurchaseAmount = Ticket::where('agent_id', $agentId)
            ->whereHas('sales', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sale_date', [$startDate, $endDate]);
            })
            ->sum('purchase_price');
            // dd($totalPurchaseAmount);
        // Calculate total payments made by the vendor
        $totalPayments = Vendor::where('agent_id', $agentId)
            ->whereBetween('start_date', [$startDate, $endDate])
//            ->count();
            ->sum('payment_amount');
            // dd($totalPayments);

        $remainingAmount = $totalPurchaseAmount - $totalPayments;

        // Count total tickets
        $ticketCount = Ticket::where('agent_id', $agentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        // dd($remainingAmount,$totalPayments,$totalPurchaseAmount);

        return response()->json([
            'total_purchase_amount' => $totalPurchaseAmount,
            'total_payments' => $totalPayments,
            'remaining_amount' => $remainingAmount,
            'ticket_count' => $ticketCount,
        ]);

    }
    public function destroy($id)
    {
        DB::beginTransaction(); // Start a transaction
//dd($id);
        try {
            // Find the vendor payment record
            $vendor = Vendor::findOrFail($id);

            // Retrieve existing vendor records for the same agent within the start_date and end_date range
            $existingVendors = Vendor::where('agent_id', $vendor->agent_id)
                ->whereBetween('start_date', [$vendor->start_date, $vendor->end_date])
                ->get();

            // Reverse the remaining balance for all related vendor records
            foreach ($existingVendors as $existingVendor) {
                $existingVendor->remaining_amount += $vendor->payment_amount; // Add back the payment amount
                $existingVendor->save();
            }

            // Delete the current vendor payment record
            $vendor->delete();

            DB::commit(); // Commit the transaction
            toastr()->success('Vendor payment deleted and balances updated successfully.');
            return back()->with('success', 'Vendor payment deleted and balances updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of error
            toastr()->error('Failed to delete vendor payment: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete vendor payment: ' . $e->getMessage());
        }
    }
    
    public function edit($id)
    {
        $payment = Vendor::findOrFail($id);
        $agents = Agent::all();

        return view('admin.vendors.create_payment', compact('payment', 'agents'));
        // 👆 form.blade.php wohi view ho sakta hai jo aap new payment ke liye use kar rahe ho
        // main aapko pehle combined form ka code de chuka hoon
    }
    public function update(Request $request, $id)
    {
        // Validate input
        $validated = $request->validate([
            'agent_id'        => 'required|exists:agents,id',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'payment_amount'  => 'required|numeric|min:0',
            'payment_method'  => 'required|string',
            'payment_date'    => 'required|date',
            'bank_name'       => 'required|string',
            'note'            => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $vendorPayment = Vendor::findOrFail($id);

            // Tickets calculation agar zarurat hai
            $tickets = Ticket::where('agent_id', $validated['agent_id'])
                ->whereBetween('created_at', [$validated['start_date'], $validated['end_date']])
                ->get();

            $totalPurchaseAmount = $tickets->sum('purchase_price');

            if ($tickets->isEmpty()) {
                return back()->with('error', 'No tickets found for this period.');
            }

            // Recalculate remaining amount
            $otherPayments = Vendor::where('agent_id', $validated['agent_id'])
                ->where('id', '!=', $vendorPayment->id)
                ->sum('payment_amount');

            $alreadyPaid = $otherPayments;
            $newRemaining = $totalPurchaseAmount - ($alreadyPaid + $validated['payment_amount']);

            if ($newRemaining < 0) {
                return back()->with('error', 'Payment amount exceeds balance.');
            }

            // Update payment
            $vendorPayment->update([
                'agent_id'        => $validated['agent_id'],
                'payment_amount'  => $validated['payment_amount'],
                'remaining_amount'=> $newRemaining,
                'payment_method'  => $validated['payment_method'],
                'payment_date'    => $validated['payment_date'],
                'start_date'      => $validated['start_date'],
                'end_date'        => $validated['end_date'],
                'note'            => $validated['note'],
                'bank_name'       => $validated['bank_name'],
            ]);

            DB::commit();

            return redirect()->route('vendors.payment.index')->with('success', 'Payment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }



}
