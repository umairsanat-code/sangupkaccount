<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WithdrawAmountTypeEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;

class RefundPaymentController extends Controller
{
    /**
     * __construct  load default values in class
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permission:Refund payments|view refund payments|
        view refund amount history|view seller documents']);
    }

    /**
     * index show all payments in payment module
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $this->timeZone = $request->timeZone;
            return \Yajra\DataTables\DataTables::of(Payment::select('*', DB::raw('SUM(clearedAmount) as amount'))
                ->with('user')->where('paymentType',WithdrawAmountTypeEnum::REFUNDED)->where('clearedAmount','>',0)->groupBy('userId')->get())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->user()->can('Transfer payments')) {
                        $btn .= ' <button data-id="' . $row->userId  . '" data-name="' . ucfirst($row->user->name)  . '"
                    class="text-white btn btn-success btn-sm btn-flat transferPayment" title="Send Money To '
                            . ucfirst($row->user->name) . '!" ><i class="fa fa-share-alt"></i> Transfer Money</button>';
                    }
                    if (auth()->user()->can('view amount history')) {
                        $btn .= ' <button data-id="' . $row->userId  . '" data-name="' . ucfirst($row->user->name)  . '"
                 class="text-white btn btn-info btn-sm btn-flat viewPayments" title="View Payment Details Of '
                            . ucfirst($row->user->name) . '!" ><i class="fa fa-folder"></i> View</button>';
                    }
                    return $btn;
                })
                ->addColumn('name', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('phone', function ($row) {
                    return $row->user ? $row->user->phoneNumber : 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->user ? $row->user->email : 'N/A';
                })

                ->rawColumns(['action', 'name', 'phone', 'email'])
                ->make(true);
        }

        return view('admin.refundPayments.payments');
    }

    /**
     * send  payments from on hold to cleared amount
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function send($id, Request $request)
    {
        try {

            if ($request->moneySent == 'yes') {
                $payments = Payment::where('userId', $id)->where('clearedAmount','>',0)->where('paymentType',WithdrawAmountTypeEnum::REFUNDED)->get();
                DB::beginTransaction();
                foreach ($payments as $sent) {
                    $not_is_null = Payment::find($sent->id);
                    if (!is_null($not_is_null)) {
                        $not_is_null->update(
                            [
                                'clearedAmount' => 0,
                                'refundedAmount' => $sent->clearedAmount
                            ]
                        );
                    }
                }
                DB::commit();
                toastr()->success('Payment sent successfully!');
                return back();
            }
            toastr()->error('Payment not sent!');
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            toastr()->error($e->getMessage());
            return back();
        }
    }


    /**
     * verify handle json data and inputs
     *
     * @param  mixed $id
     * @return void
     */
    public function verify($id)
    {
        $payment = Payment::with('user')
            ->select('*', DB::raw('SUM(clearedAmount) as amount'))
            ->where('userId',$id)->where('clearedAmount','>',0)
            ->where('paymentType',WithdrawAmountTypeEnum::REFUNDED)->first();
        return response()->json(['status' => true, 'data' => $payment], JsonResponse::HTTP_OK);
    }

    /**
     * show specific resource
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        $user = Payment::with('user')->where('userId', $id)
            ->where('clearedAmount','>', 0)->where('paymentType',WithdrawAmountTypeEnum::REFUNDED)->take(11)->get();
        return response()->json(['status' => true, 'data' => $user], JsonResponse::HTTP_OK);
    }
    /**
     * history show all payments against user orders
     *
     * @param  mixed $id
     * @return void
     */
    public function history($id)
    {
        $paymentHistories=Payment::with('order')->where('clearedAmount','>', 0)
            ->where('paymentType',WithdrawAmountTypeEnum::REFUNDED)->where('userId',$id)->get();
        return view('admin.refundPayments.paymentHistory', compact('paymentHistories'));
    }

    /**
     * documents show all seller documents against user id
     *
     * @param  mixed $id
     * @return void
     */
    public function documents($id)
    {
        $user = User::with(['documents', 'documents.images'])->find($id);
        return response()->json(['status' => true, 'data' => $user], JsonResponse::HTTP_OK);
    }
}
