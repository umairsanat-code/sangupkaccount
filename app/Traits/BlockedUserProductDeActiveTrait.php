<?php
namespace App\Traits;

use App\Models\Cart;
use App\Models\FavouriteProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait BlockedUserProductDeActiveTrait{
    public function productDeActive($userId){
        try {
            DB::beginTransaction();
            Product::where('sellerId',$userId)->update(['isSellerBlocked'=>1]);
            $productBlockeds = Product::select('id')->where('sellerId',$userId)->where('isSellerBlocked',1)->get()->pluck('id');
            Cart::whereIn('productId',$productBlockeds)->delete();
            FavouriteProduct::whereIn('productId',$productBlockeds)->delete();
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            $data = [
                'Time' => gmdate("F j, Y, g:i a"),
                'Status Code' => $e->getCode(),
                'Message' => $e->getMessage(),
            ];
            Log::error("DeActive BlockedUser",$data);
            return false;
        }

    }
    public function productActive($userId){
        try {
            DB::beginTransaction();
            Product::where('sellerId',$userId)->update(['isSellerBlocked'=>0]);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            $data = [
                'Time' => gmdate("F j, Y, g:i a"),
                'Status Code' => $e->getCode(),
                'Message' => $e->getMessage(),
            ];
            Log::error("Active Blocked User",$data);
            return false;
        }

    }
}
