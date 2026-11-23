<?php

namespace App\Services;

use App\Traits\loggerExceptionTrait;
use Exception;
use Illuminate\Support\Facades\Http;

class FacebookService
{


    use loggerExceptionTrait;

    public function getToken($token){

        $app_id = env('Facebook_APP_ID');
        $app_secret = env('Facebook_APP_SECRET');
        $getUserPageUrl = "https://graph.facebook.com/v16.0/me?access_token=".$token;
        try {
            $getUser =  Http::get($getUserPageUrl);
            $getUser->throw();
            $jsonObject = json_decode($getUser);
            $userId=$jsonObject->id;
            $urlGetFields="https://graph.facebook.com/v16.0/$userId?fields=id,first_name,last_name,email,picture&access_token=".$token;
            $getFields=Http::get($urlGetFields);
            $userData = json_decode($getFields);
            return [
                'email' => $userData->email,
                'firstName' => $userData->first_name,
                'lastName' => $userData->last_name,
                'picture' => $userData->picture->data->url,
                'socialId' => $userData->id,
            ];

        }catch (\Exception $e){
            $this->saveExceptionLog($e, 'Facebook Service exception');
            return false;
        }
    }



}
