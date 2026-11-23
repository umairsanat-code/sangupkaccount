<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $expenses = Expense::query();

            // Apply date range filter if provided
            if ($request->startDate && $request->endDate) {
                $expenses->whereBetween('date', [$request->startDate, $request->endDate]);
            }
 $expenses->orderBy('date', 'desc');
            $totalAmount = $expenses->sum('amount');
            $totalCount = $expenses->count();

            return DataTables::of($expenses->select(['id', 'date', 'category', 'amount', 'description']))
                ->addColumn('actions', function ($expense) {
                $authUserId = auth()->id(); // Get logged-in user ID
            
                $btn = '';
            
                // Sirf user ID 1 ke liye Edit aur Delete buttons dikhayen
                if ($authUserId == 1) {
                    $btn .= '
                        <button class="btn btn-warning btn-sm"
                            data-toggle="modal"
                            data-target="#editExpenseModal"
                            data-id="' . $expense->id . '"
                            data-date="' . $expense->date . '"
                            data-category="' . $expense->category . '"
                            data-amount="' . $expense->amount . '"
                            data-description="' . $expense->description . '">
                            Edit
                        </button>
                        <form action="' . route('expenses.destroy', $expense) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    ';
                }
            
                return $btn;
            })

                ->rawColumns(['actions'])
                ->with([
                    'totalAmount' => $totalAmount,
                    'totalCount' => $totalCount,
                ])
                ->make(true);
        }

        return view('admin.expenses.index');
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'category' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        Expense::create($request->all());
        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'date' => 'required|date',
            'category' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $expense->update($request->all());
        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
