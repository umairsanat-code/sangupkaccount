<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    /**
     * Show the activity log report page.
     */
    public function index()
    {
        $users = User::orderBy('firstName')->get();
        return view('admin.activity_log.index', compact('users'));
    }

    /**
     * Return DataTable JSON for activity logs.
     */
    public function getData(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Action filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('date_time', function ($log) {
                return Carbon::parse($log->created_at)->format('d M Y, h:i A');
            })
            ->addColumn('action_badge', function ($log) {
                $color = $log->action_color;
                $label = strtoupper(str_replace('_', ' ', $log->action));
                return "<span class=\"badge bg-{$color}\">{$label}</span>";
            })
            ->rawColumns(['action_badge'])
            ->make(true);
    }

    /**
     * Delete all logs EXCEPT today's — keep current day only.
     */
    public function resetLog(Request $request)
    {
        $today = Carbon::today();
        $deleted = ActivityLog::whereDate('created_at', '<', $today)->delete();

        return response()->json([
            'success' => true,
            'message' => "Reset complete! {$deleted} old record(s) deleted. Today's logs are safe.",
        ]);
    }
}
