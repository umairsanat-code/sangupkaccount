<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profit;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $authId = Auth::id();

        if ($authId == 1) {
            // Admin - get all active users
            $users = User::whereIn('status', ['Active','Banned'])->get();
        } else {
            // Normal user - get only self
            $users = User::where('id', $authId)->where('status', 'active')->get();
        }

        // Process each user
        $users = $users->map(function ($user) {
            $authorName = $user->firstName . ' ' . $user->lastName;

            // Tickets of this user
            $tickets = Ticket::where('author_name', $authorName)->get();

            $totalSales = 0;
            $totalProfit = 0;

            foreach ($tickets as $ticket) {
                $salesSum = Sale::where('ticket_id', $ticket->id)->sum('sale_price');
                $profitSum = Profit::whereHas('sale', function ($query) use ($ticket) {
                    $query->where('ticket_id', $ticket->id);
                })->sum('profit_amount');

                $ticket->total_sales = $salesSum;
                $ticket->total_profit = $profitSum;

                $totalSales += $salesSum;
                $totalProfit += $profitSum;
            }

            $user->tickets = $tickets;
            $user->total_tickets = $tickets->count();
            $user->total_sales = $totalSales;
            $user->total_profit = $totalProfit;

            return $user;
        });

        return view('admin.users.buyers.buyer', compact('users'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        // Create new member
     $user= User::create([
            'firstName' => $request->first_name,
            'lastName'  => $request->last_name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
        ]);
 $user->assignRole('seller');
        return back()->with('success', 'Member added successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
//        dd($request->all(),$user);
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return response()->json(['message' => 'User updated']);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'Active' ? 'Banned' : 'Active';
        $user->save();

        return response()->json(['message' => 'Status updated']);
    }

     
         public function getPendingTickets(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $authorName = $user->firstName . ' ' . $user->lastName;

        $tickets = Ticket::with('customer')
            ->where('author_name', $authorName)
            ->whereHas('sales', function ($query) {
                $query->where('pending_amount', '>', 0);
            })
            ->with(['sales' => function ($query) {
                $query->where('pending_amount', '>', 0);
            }])
            ->latest()
            ->get();

        // Total pending
        $totalPendingAmount = $tickets->flatMap->sales->sum('pending_amount');

        // Format response
        $response = [];
        foreach ($tickets as $ticket) {
            foreach ($ticket->sales as $sale) {
                $response[] = [
                    'customer'       => $ticket->customer->name ?? 'N/A',
                    'ticket_number'  => $ticket->ticket_number,
                    'issue_price'    => $ticket->purchase_price,
                    'sale_price'     => $sale->sale_price,
                    'paid'           => $sale->total_amount_paid,
                    'pending'        => $sale->pending_amount,
                    'sale_date'      => $ticket->pnr,
                    'profit'         => $sale->sale_price - $ticket->purchase_price,
                ];
            }
        }

        // If request is AJAX → return JSON
        if ($request->ajax()) {
            return datatables()->of($response)->make(true);
        }

        // Else return Blade
        return view('admin.users.buyers.pending', [
            'userId' => $userId,
            'totalPendingAmount' => $totalPendingAmount,
        ]);
    }






}
