<?php


namespace App\Services;


use App\Models\FcmToken;
use Illuminate\Support\Facades\DB;

class FcmTokenService
{
     public function addFcmtoken($request, $userId){
         $isAlreadyHaveDeviceIdentifier = FcmToken::where('deviceidentifier', $request->deviceIdentifier)
             ->where('userId',$userId)->first();
         if (is_null($isAlreadyHaveDeviceIdentifier)) {
             DB::beginTransaction();
             $fcm = FcmToken::create([
                 'userId' => $userId,
                 'token' => $request->fcmToken,
                 'deviceidentifier' => $request->deviceIdentifier
             ]);
             DB::commit();
         }else{

             /* if user login with same device then update the token with old token  */
             $isAlreadyHaveDeviceIdentifier->update(array('token'=>$request->fcmToken,'isActive'=>1));
         }
     }
    public function getUserFCMRegistrationToken(int $userId)
    {
        $FCMRegistrationTokens = FCMToken::select('token')->where('isActive',1)->where('userId', $userId)->get()->toArray();
        return array_column($FCMRegistrationTokens, 'token');
    }
}
