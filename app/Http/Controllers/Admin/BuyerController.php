<?php

namespace App\Http\Controllers\Admin;

use App\Traits\BlockedUserProductDeActiveTrait;
use Exception;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\loggerExceptionTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BuyerController extends Controller
{
    use FileUploadTrait, loggerExceptionTrait,BlockedUserProductDeActiveTrait;

    public function __construct()
    {
        $this->middleware(['permission:Block Buyers|View Buyer Details|View Buyers']);
    }
    /**
     * listing  : list all category using yajra/laravel-datatables-oracle: "~9.0"
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        if ($request->ajax()) {
            $this->timeZone = $request->timeZone;
            return \Yajra\DataTables\DataTables::of(User::role('buyer')->get())
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn='';
                if(auth()->user()->can('Block Buyers')){
                    if($row->status == UserStatus::BANNED){
                        $btn .= ' <a href="'.url('users/buyers/Active/'.$row->id.'/status').'" data-id="' . $row->id  . '" data-name="' . ucfirst($row->name)  . '" class="btn btn-danger btn-sm unbllockBuyer" onclick="return  confirm(\'Are you sure to unblock ' .fullName($row->firstName,$row->lastName).' ?\')" style="background:white;border:white" title="Unblock '.fullName($row->firstName,$row->lastName).'!"><i class="fa fa-check text-success"></i></a>';
                    }else{
                        $btn .= ' <a href="'.url('users/buyers/Banned/'.$row->id.'/status').'" data-id="' . $row->id  . '" data-name="' . ucfirst($row->name)  . '" class="btn btn-danger btn-sm blockBuyer" onclick="return  confirm(\'Are you sure to block ' .fullName($row->firstName,$row->lastName).' ?\')" style="background:white;border:white" title="Block '.fullName($row->firstName,$row->lastName).'!"><i class="fa fa-ban text-danger"></i></a>';
                    }
                }

               $btn .= ' <button data-id="' . $row->id  . '" data-name="' . ucfirst($row->name)  . '"  class="text-white btn btn-info btn-sm btn-flat viewBuyer" title="View Details!"><i class="fa fa-folder"></i> View</button>';
                return $btn;
            })
            ->addColumn('facebook', function ($row) {
                if ($row->fbId){
                    $btn = 'Yes';
                    return $btn;
                }else{
                    return  'No';
                }
            })
            ->addColumn('google', function ($row) {
                if ($row->googleId){
                    $btn = 'Yes';
                    return $btn;
                }else{
                    return  'No';
                }
            })
            ->addColumn('status', function ($row) {
                return getStatusHtml($row->status);
             })
             ->editColumn('name', function ($row) {
                $img='<div class="row">';
                $img.='<div class="col-md-4 col-12 pr-0">';
                 $img.='<img class=" img-fluid img-circle img-size-32" src="' . $row->AvatarFullUrl . '" style="height:35px;">';
                 $img.='</div>';
                 $img.='<div class="col-md-8 col-12 pl-0">';
                 $img.= $row->name;
                 $img.='</div>';
                 $img.='</div>';
                return $img;
            })
                ->rawColumns(['name','status', 'action', 'facebook', 'google'])
                ->make(true);
        }
        return view('admin.users.buyers.buyer');
    }

        /**
     * get : get buyer details to show in aside bar
     *
     * @param  int $id
     * @return \Illuminate\Support\Facades\Response
     */
    public function get($id)
    {
        $response = User::with(['documents','buyerOrders','buyerOrdersActive','buyerOrdersActive.orderItems'
        ,'buyerOrders.orderItems','buyerOrdersActive.orderItems.product','buyerOrders.orderItems.product'])->find($id);
        return response()->json(['status' => true, 'data' => $response], JsonResponse::HTTP_OK);
    }


    /**
     * block : block a specific resource
     *
     * @param  mixed $status
     * @param  mixed $id
     * @return void
     */
    public function block($status, $id)
    {
        try {
            DB::beginTransaction();
            $isBanned = User::find($id)->update(['status' => UserStatus::BANNED]);
            if ($isBanned) {
                $isBanned->tokens()->delete();
                $this->productDeActive($id);
                DB::commit();
                toastr()->success('User banned successfully!');
            }
            return back();
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'BuyerController exception');
            toastr()->error($e->getMessage());
            return back();
        }
    }

       /**
     * status : get the status with user id and update the status from Approved to Rejected to Approved
     *
     * @param  mixed $status
     * @param  mixed $id
     * @return void
     */
    public function status($status, $id)
    {
        try {
            DB::beginTransaction();
            $user = User::find($id);
            $name=$user->firstName.' '.$user->lastName;
            if ($status == UserStatus::BANNED) {
                $user->tokens()->delete();
                $user->update(['status' => UserStatus::BANNED]);
                $this->productDeActive($user->id);
                DB::commit();
                toastr()->success($name.' Blocked successfully!');
                return back();
            }
            if ($status == UserStatus::ACTIVE) {
                $user->update(['status' => UserStatus::ACTIVE]);
                $this->productActive($user->id);
                DB::commit();
                toastr()->success($name.' Unblocked successfully!');
                return back();
            }

           return back();
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'BuyerController exception');
            toastr()->error($e->getMessage());
            return back();
        }
    }
}
