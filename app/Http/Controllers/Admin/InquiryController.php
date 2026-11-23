<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Inquiry::query()->with('user');

            if ($request->filled('status') && in_array($request->status, ['Pending', 'Done'])) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addColumn('date', function ($q) {
                    return $q->created_at ? Carbon::parse($q->created_at)->format('m/d/Y, h:i:s A') : '-';
                })
                ->addColumn('added_by', function ($q) {
                    return $q->user ? e($q->user->name) : 'N/A';
                })
                ->addColumn('passenger', function ($q) {
                    $phone = $q->phone_number ? '<span class="text-muted"><i class="fa fa-phone"></i> ' . e($q->phone_number) . '</span>' : '';
                    return '<strong>' . e($q->passenger_name) . '</strong><br>' . $phone;
                })
                ->addColumn('visa_price', function ($q) {
                    return $q->visa_price !== null ? number_format((float) $q->visa_price, 2) : '-';
                })
                ->addColumn('payment_received', function ($q) {
                    return $q->payment_received !== null ? number_format((float) $q->payment_received, 2) : '-';
                })
                ->addColumn('priority', function ($q) {
                    $badge = $q->priority === 'High' ? 'danger' : ($q->priority === 'Low' ? 'success' : 'secondary');
                    return '<span class="badge bg-' . $badge . '">' . e($q->priority) . '</span>';
                })
                ->addColumn('status', function ($q) {
                    $badge = $q->status === 'Done' ? 'success' : 'warning';
                    return '<span class="badge bg-' . $badge . '">' . e($q->status) . '</span>';
                })
                ->addColumn('actions', function ($q) {
                    $editBtn = '<button type="button" class="btn btn-info btn-sm edit-inquiry-btn" data-id="' . $q->id . '"><i class="fa fa-edit"></i></button>';
                    $statusBtn = $q->status === 'Done'
                        ? '<button type="button" class="btn btn-warning btn-sm mark-status-btn" data-id="' . $q->id . '" data-status="Pending"><i class="fa fa-undo"></i> Pending</button>'
                        : '<button type="button" class="btn btn-success btn-sm mark-status-btn" data-id="' . $q->id . '" data-status="Done"><i class="fa fa-check"></i> Done</button>';
                    $deleteBtn = '<button type="button" class="btn btn-danger btn-sm delete-inquiry-btn" data-id="' . $q->id . '"><i class="fa fa-trash"></i></button>';
                    return $editBtn . ' ' . $statusBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['passenger', 'priority', 'status', 'actions'])
                ->make(true);
        }

        $counts = [
            'all' => Inquiry::count(),
            'Pending' => Inquiry::where('status', 'Pending')->count(),
            'Done' => Inquiry::where('status', 'Done')->count(),
        ];

        return view('admin.inquiries.index', compact('counts'));
    }

    public function counts()
    {
        return response()->json([
            'all' => Inquiry::count(),
            'Pending' => Inquiry::where('status', 'Pending')->count(),
            'Done' => Inquiry::where('status', 'Done')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'visa_price' => 'nullable|numeric|min:0',
            'payment_received' => 'nullable|numeric|min:0',
        ]);

        $inquiry = Inquiry::create([
            'user_id' => auth()->id(),
            'passenger_name' => $data['passenger_name'],
            'phone_number' => $data['phone_number'] ?? null,
            'priority' => $data['priority'] ?: 'Normal',
            'status' => $data['status'] ?: 'Pending',
            'description' => $data['description'] ?? null,
            'visa_price' => $data['visa_price'] ?? null,
            'payment_received' => $data['payment_received'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Inquiry added successfully!', 'inquiry' => $inquiry]);
    }

    public function edit($id)
    {
        $inquiry = Inquiry::findOrFail($id);

        return response()->json($inquiry);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'visa_price' => 'nullable|numeric|min:0',
            'payment_received' => 'nullable|numeric|min:0',
        ]);

        $inquiry = Inquiry::findOrFail($id);
        $inquiry->update([
            'passenger_name' => $data['passenger_name'],
            'phone_number' => $data['phone_number'] ?? null,
            'priority' => $data['priority'] ?: 'Normal',
            'status' => $data['status'] ?: 'Pending',
            'description' => $data['description'] ?? null,
            'visa_price' => $data['visa_price'] ?? null,
            'payment_received' => $data['payment_received'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Inquiry updated successfully!']);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:inquiries,id',
            'status' => 'required|in:Pending,Done',
        ]);

        $inquiry = Inquiry::findOrFail($request->id);
        $inquiry->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated to ' . $request->status . '!']);
    }

    public function destroy($id)
    {
        Inquiry::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Inquiry deleted successfully!']);
    }

    public function export(Request $request)
    {
        $query = Inquiry::query()->with('user');

        if ($request->filled('status') && in_array($request->status, ['Pending', 'Done'])) {
            $query->where('status', $request->status);
        }

        $inquiries = $query->orderBy('created_at', 'desc')->get();

        $fileName = 'inquiries_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($inquiries) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Passenger Name', 'Phone Number', 'Priority', 'Status', 'Description', 'Visa Price', 'Payment Received', 'Added By', 'Date']);

            foreach ($inquiries as $inquiry) {
                fputcsv($handle, [
                    $inquiry->passenger_name,
                    $inquiry->phone_number,
                    $inquiry->priority,
                    $inquiry->status,
                    $inquiry->description,
                    $inquiry->visa_price,
                    $inquiry->payment_received,
                    $inquiry->user ? $inquiry->user->name : '',
                    $inquiry->created_at ? Carbon::parse($inquiry->created_at)->toDateTimeString() : '',
                ]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}