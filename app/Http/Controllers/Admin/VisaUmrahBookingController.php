<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\VisaUmrahBooking;
use App\Models\VisaUmrahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VisaUmrahBookingController extends Controller
{
    protected $type;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->type = $request->route()->parameter('type') ?? 'visa';
            return $next($request);
        });
    }

    public function index(Request $request, $type)
    {
        if ($request->ajax()) {
            $query = VisaUmrahBooking::query()
                ->where('booking_type', $type)
                ->with('mainAgency')
                ->withSum('services as services_client_sum', 'client_price')
                ->withSum('services as services_agency_sum', 'agency_cost')
                ->withSum('clientPayments as client_received_sum', 'amount')
                ->withSum('agencyPayments as agency_paid_sum', 'amount')
                ->withSum('expenses as expenses_sum', 'amount');

            if ($request->filled('status')) {
                $query->where('visa_status', $request->status);
            }
            if ($request->filled('booking_status')) {
                $query->where('booking_status', $request->booking_status);
            }
            if ($request->filled('agency_id')) {
                $query->where('main_agency_id', $request->agency_id);
            }
            if ($request->filled('client_name')) {
                $query->where('client_name', 'like', '%' . $request->client_name . '%');
            }
            if ($request->filled('travel_date_from') && $request->filled('travel_date_to')) {
                $query->whereBetween('travel_date', [$request->travel_date_from, $request->travel_date_to]);
            }

            return DataTables::of($query)
                ->addColumn('agency_name', function ($row) {
                    return $row->mainAgency ? $row->mainAgency->name : '-';
                })
                ->addColumn('client_received_sum', function ($row) {
                    return (float) $row->client_received_sum;
                })
                ->addColumn('agency_paid_sum', function ($row) {
                    return (float) $row->agency_paid_sum;
                })
                ->addColumn('services_client_sum', function ($row) {
                    return (float) $row->services_client_sum;
                })
                ->addColumn('services_agency_sum', function ($row) {
                    return (float) $row->services_agency_sum;
                })
                ->addColumn('expenses_sum', function ($row) {
                    return (float) $row->expenses_sum;
                })
                ->addColumn('client_total_full', function ($row) {
                    return (float) $row->main_client_total + (float) $row->services_client_sum;
                })
                ->addColumn('client_pending', function ($row) {
                    $total = (float) $row->main_client_total + (float) $row->services_client_sum;
                    return $total - (float) $row->client_received_sum;
                })
                ->addColumn('agency_total_full', function ($row) {
                    return (float) $row->main_agency_cost + (float) $row->services_agency_sum;
                })
                ->addColumn('agency_pending', function ($row) {
                    $total = (float) $row->main_agency_cost + (float) $row->services_agency_sum;
                    return $total - (float) $row->agency_paid_sum;
                })
                ->addColumn('gross_profit', function ($row) {
                    $client = (float) $row->main_client_total + (float) $row->services_client_sum;
                    $agency = (float) $row->main_agency_cost + (float) $row->services_agency_sum;
                    return $client - $agency;
                })
                ->addColumn('net_profit', function ($row) {
                    $client = (float) $row->main_client_total + (float) $row->services_client_sum;
                    $agency = (float) $row->main_agency_cost + (float) $row->services_agency_sum;
                    return $client - $agency - (float) $row->expenses_sum;
                })
                ->addColumn('status_badge', function ($row) {
                    $badges = [
                        'pending' => 'warning', 'documents_required' => 'info',
                        'processing' => 'primary', 'approved' => 'success',
                        'rejected' => 'danger', 'completed' => 'dark',
                        'cancelled' => 'secondary',
                        'new' => 'secondary', 'confirmed' => 'primary',
                        'active' => 'success',
                    ];
                    $status = $row->isUmrah() ? ($row->booking_status ?: 'new') : ($row->visa_status ?: 'pending');
                    $color = $badges[$status] ?? 'secondary';
                    return '<span class="badge badge-' . $color . '">' . ucwords(str_replace('_', ' ', $status)) . '</span>';
                })
                ->addColumn('services', function ($row) {
                    return $this->serviceSummary($row);
                })
                ->addColumn('action', function ($row) {
                    $routePrefix = $row->booking_type === 'visa' ? 'visa_umrah.bookings' : 'visa_umrah.bookings';
                    return '
                    <a href="' . route($routePrefix . '.show', ['type' => $row->booking_type, 'booking' => $row->id]) . '" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>
                    <a href="' . route($routePrefix . '.edit', ['type' => $row->booking_type, 'booking' => $row->id]) . '" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                    <form action="' . route($routePrefix . '.destroy', ['type' => $row->booking_type, 'booking' => $row->id]) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Delete this booking?\');">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-xs btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>';
                })
                ->rawColumns(['status_badge', 'services', 'action'])
                ->make(true);
        }

        $agencies = Agency::orderBy('name')->get();
        return view('admin.visa_umrah.bookings.index', compact('type', 'agencies'));
    }

    protected function serviceSummary($row)
    {
        $labels = $row->services->map(function ($s) {
            return '<span class="badge badge-light border">' . ucfirst($s->service_type) . ': ' . e($s->name ?? $s->pickup_location ?? '-') . '</span>';
        })->implode(' ');
        return $labels ?: '-';
    }

    public function create($type)
    {
        $agencies = Agency::orderBy('name')->get();
        return view('admin.visa_umrah.bookings.create', compact('type', 'agencies'));
    }

    public function store(Request $request, $type)
    {
        $data = $request->validate($this->rules($type));

        DB::beginTransaction();
        try {
            $bookingNo = $this->generateBookingNo($type);

            $booking = VisaUmrahBooking::create(array_merge($data, [
                'booking_type' => $type,
                'booking_no' => $bookingNo,
                'main_agency_cost' => $data['main_agency_cost'] ?? 0,
                'main_client_total' => $data['main_client_total'] ?? 0,
                'visa_status' => $data['visa_status'] ?? 'pending',
                'booking_status' => $data['booking_status'] ?? 'new',
                'created_by' => Auth::id(),
            ]));

            DB::commit();
            toastr()->success('Booking #' . $bookingNo . ' created successfully.');
            return redirect()->route('visa_umrah.bookings.show', ['type' => $type, 'booking' => $booking->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($type, $id)
    {
        $booking = VisaUmrahBooking::with([
            'mainAgency', 'services.agency', 'services.payments',
            'payments', 'documents', 'expenses',
        ])->findOrFail($id);

        $agencies = Agency::orderBy('name')->get();

        return view('admin.visa_umrah.bookings.show', compact('type', 'booking', 'agencies'));
    }

    public function edit($type, $id)
    {
        $booking = VisaUmrahBooking::findOrFail($id);
        $agencies = Agency::orderBy('name')->get();
        return view('admin.visa_umrah.bookings.edit', compact('type', 'booking', 'agencies'));
    }

    public function update(Request $request, $type, $id)
    {
        $booking = VisaUmrahBooking::findOrFail($id);
        $data = $request->validate($this->rules($type));

        DB::beginTransaction();
        try {
            $booking->update(array_merge($data, [
                'main_agency_cost' => $data['main_agency_cost'] ?? 0,
                'main_client_total' => $data['main_client_total'] ?? 0,
                'visa_status' => $data['visa_status'] ?? $booking->visa_status,
                'booking_status' => $data['booking_status'] ?? $booking->booking_status,
            ]));

            DB::commit();
            toastr()->success('Booking updated successfully.');
            return redirect()->route('visa_umrah.bookings.show', ['type' => $type, 'booking' => $booking->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($type, $id)
    {
        $booking = VisaUmrahBooking::findOrFail($id);
        $booking->payments()->delete();
        $booking->services()->delete();
        $booking->documents()->delete();
        $booking->expenses()->delete();
        $booking->delete();

        toastr()->success('Booking deleted.');
        return redirect()->route('visa_umrah.bookings.index', $type);
    }

    protected function rules($type)
    {
        $base = [
            'client_name' => 'required|string',
            'client_phone' => 'nullable|string',
            'client_email' => 'nullable|email',
            'passport_number' => 'nullable|string',
            'nationality' => 'nullable|string',
            'travel_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'main_agency_id' => 'nullable|exists:agencies,id',
            'main_agency_reference' => 'nullable|string',
            'main_agency_cost' => 'nullable|numeric|min:0',
            'main_client_total' => 'nullable|numeric|min:0',
        ];

        if ($type === 'visa') {
            $base['visa_country'] = 'nullable|string';
            $base['visa_type'] = 'nullable|string';
            $base['num_travelers'] = 'nullable|integer|min:1';
            $base['visa_status'] = 'nullable|in:' . implode(',', VisaUmrahBooking::VISA_STATUSES);
        } else {
            $base['num_adults'] = 'nullable|integer|min:1';
            $base['num_children'] = 'nullable|integer|min:0';
            $base['umrah_package'] = 'nullable|string';
            $base['booking_status'] = 'nullable|in:' . implode(',', VisaUmrahBooking::BOOKING_STATUSES);
        }

        return $base;
    }

    protected function generateBookingNo($type)
    {
        $prefix = $type === 'visa' ? 'VSA' : 'UMR';
        $year = date('Y');
        $last = VisaUmrahBooking::where('booking_no', 'like', $prefix . '-' . $year . '-%')
            ->orderByDesc('booking_no')
            ->value('booking_no');

        $next = 0;
        if ($last) {
            $parts = explode('-', $last);
            $next = (int) end($parts);
        }
        $next++;

        return $prefix . '-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function print($type, $id)
    {
        $booking = VisaUmrahBooking::with([
            'mainAgency', 'services.agency', 'services.payments',
            'payments', 'documents', 'expenses',
        ])->findOrFail($id);

        return view('admin.visa_umrah.bookings.print', compact('type', 'booking'));
    }
}