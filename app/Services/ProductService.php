<?php


namespace App\Services;


use App\Models\PersonalAccessToken;
use App\Models\Product;
use App\Models\UserBlock;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function productSearch(array $args)
    {
        $auth_id = Auth::id();
        $userBlock=UserBlock::where('blockedUserId',Auth::id())->where('status','!=','UNBLOCK')->pluck('blockedBy')->toArray();
        $productBlock=[0];
        if ($userBlock!=null){
            $productBlock=$userBlock;
        }

        $userBlockProduct=UserBlock::where('blockedBy',Auth::id())->where('status','!=','UNBLOCK')->pluck('blockedUserId')->toArray();
        $productBlockUser=[0];
        if ($userBlockProduct!=null){
            $productBlockUser=$userBlockProduct;
        }
        $arrayMerge=array_merge($productBlock,$productBlockUser);
        $query = Product::select('*')->where('isSellerBlocked',0)->where('inStockQuantity','!=',0);
        if ($auth_id) {
            $query = $query->where('sellerId', '!=', $auth_id)
            ->whereNotIn('sellerId', $arrayMerge)
            ;
        }
        if ($args['searchQuery']) {
            $query = $query->where('name', 'LIKE', "%{$args['searchQuery']}%");
        }
        if ($args['subcategoryId']) {
            $query = $query->where('subCategoryId', $args['subcategoryId']);
        }

        if ($args['categoryId']) {
            $query = $query->where('categoryId', $args['categoryId']);
        }
        if (isset($args['latitude']) && isset($args['longitude'])) {
            $longitude = $args['longitude'];
            $latitude = $args['latitude'];
            $query=$query->selectRaw("
                     ( 3959 * acos( cos( radians(?) ) *
                       cos( radians( latitude ) )
                       * cos( radians( longitude ) - radians(?)
                       ) + sin( radians(?) ) *
                       sin( radians( latitude ) ) )
                     ) AS distance", [$latitude, $longitude, $latitude])->having("distance", "<=", 20)->orderBy("distance", 'asc');

        }


        return $query->with(['user','imageProducts','favouriteProduct','orderItems','cart'])->latest()->paginate($args['limit']);

    }


}
