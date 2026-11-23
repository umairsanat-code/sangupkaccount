<?php

namespace App\Traits;
use App\Services\FcmTokenService;
use App\Services\FirebaseService;

trait FirebaseNotification
{
    public function sendNotificationToBuyer($fcm, $userId)
    {
        $FCMToken = new FcmTokenService();
        $firebase = new FirebaseService();
        $token = $FCMToken->getUserFCMRegistrationToken($userId);
        info('firebaseData',['fcmDetail'=>$fcm,'userFCMToken',$token]);
        foreach ($token as $key => $token) {
            $fcm['FCMRegistrationToken'] = $token;
            $firebase->cloudMessageToSingleDevice($fcm);
        }
    }
}
