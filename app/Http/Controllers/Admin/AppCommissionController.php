<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;

class AppCommissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin']);
    }

    public function listing(){
        $commission=CommissionSetting::first();
        return view('admin.commission.listing',compact('commission'));
    }

    public function update(Request $request){
        $commissionSetting=CommissionSetting::first();
        if(is_null($commissionSetting)){
            CommissionSetting::create([
                'amount'=>$request->commission
            ]);
        }else{
            $commissionSetting->update(['amount'=>$request->commission]);
        }
        toastr()->success('Commission updated successfully!');
        return back();

    }
}
