<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AirlineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        dd($request->ajax());
        if ($request->ajax()) {
            $data = Airline::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                $authUserId = auth()->id(); // Get logged-in user ID
            
                $btn = '';
            
                // Sirf user ID 1 ke liye Edit aur Delete buttons dikhayen
                if ($authUserId == 1) {
                    $btn .= '<button type="button" data-id="'.$row->id.'" class="edit btn btn-success btn-sm">Edit</button>';
                    $btn .= '<button type="button" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                }
            
                return $btn;
})

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.airline.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'country' => 'required|string|max:100',
        ]);

        Airline::create($request->all());
        return response()->json(['success' => 'Airline added successfully.']);
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
        $airline = Airline::findOrFail($id);
        return response()->json($airline);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'country' => 'required|string|max:100',
        ]);

        $airline = Airline::findOrFail($id);
        $airline->update($request->all());

        return response()->json(['success' => 'Airline updated successfully.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $airline = Airline::findOrFail($id);

        // Check if the airline is associated with any ticket
        $ticketCount = $airline->tickets()->count(); // Assuming a relationship `tickets()` is defined in the Airline model

        if ($ticketCount > 0) {
            return response()->json(['error' => 'Cannot delete this airline. It is associated with one or more tickets.'], 400);
        }

        // If no tickets are associated, proceed with deletion
        $airline->delete();

        return response()->json(['success' => 'Airline deleted successfully.']);
    }


}
