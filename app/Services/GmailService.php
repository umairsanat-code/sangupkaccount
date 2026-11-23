<?php

namespace App\Services;

use Session;
use Google_Client;
use Google_Exception;
use Google_Service_Oauth2;
use App\Traits\loggerExceptionTrait;


class GmailService
{
    use loggerExceptionTrait;
    protected $client;
    protected $googleOAuth;


    function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Demo App');
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URL'));
        $this->googleOAuth = new Google_Service_Oauth2($this->client);
        $this->client->addScope('email');
        $this->client->addScope('profile');

    }

    function getInfoByToken($token)
    {
         try {
                $this->client->setAccessToken($token);
                $me = $this->googleOAuth->userinfo->get();
                if (!$me) {
                    return false;
                }
                return [
                    'socialId' => $me->id,
                    'email' => $me->email,
                    'firstName' => $me->getGivenName(),
                    'lastName' => $me->getFamilyName(),
                    'avatar' => $me->picture,
                ];

            } catch (Google_Exception $e) {
                $this->saveExceptionLog($e, 'Gmail Service exception');
                return false;
            }

    }
}



