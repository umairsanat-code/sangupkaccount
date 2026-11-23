<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use stdClass;

class FirebaseService
{
    public $factory;

//   Library :  composer require kreait/firebase-php
    public function __construct()
    {
        $this->factory = (new Factory())
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $this->auth = $this->factory->createAuth();
    }
    public function getRemoteConfigValue($name)
    {
        $remoteConfig = $this->factory->createRemoteConfig()->get();
        $config = json_decode(json_encode($remoteConfig->jsonSerialize()['parameters'][$name], true));
        return $config->defaultValue->value;
    }

    public function deleteFirebaseUser()
    {

        try {
            $users = $this->auth->listUsers();
            foreach ($users as $user) {
                /** @var \Kreait\Firebase\Auth\UserRecord $user */
                  $this->auth->deleteUser($user->uid);
            }
        }catch (Firebase\Exception\FirebaseException $e){
            return  false;
        }
    }
    public function createUserAccount(array $data)
    {
        $addUser = array(
            'email' => $data['email'],
            'password' => $data['password']
        );
        try {
            $createdUser = $this->auth->createUser($addUser);
            $createdUser->data = $createdUser;
            $createdUser->status = true;
        }catch (Firebase\Exception\FirebaseException $e) {
            $createdUser = new stdClass();
            $createdUser->message = $e->getMessage();
            $createdUser->status = false;
        }
        return $createdUser;
    }
    public function getToken(string $uid): string
    {
        return $this->auth->createCustomToken($uid)->toString();
    }

    public function cloudMessageToSingleDevice($data)
    {
        $messaging = $this->factory->createMessaging();
        $title = $data['title'] ?? null;
        $body = $data['body'] ?? null;
        $imageUrl = $data['imageUrl'] ?? null;
        $notification = Notification::create($title, $body, $imageUrl);
        try {
            $message = CloudMessage::withTarget('token', $data['FCMRegistrationToken'])
                ->withNotification($notification)
                ->withData($data['data'])->withDefaultSounds();
            $message = $messaging->send($message);
            Log::info('Firebase Notification', ['messageId' => $message, 'token' => $data['FCMRegistrationToken'], 'data' => $data['data']]);
        } catch (Firebase\Exception\FirebaseException $e) {
            $data = [
                'Time' => gmdate("F j, Y, g:i a"),
                'Status Code' => $e->getCode(),
                'Message' => $e->getMessage(),
            ];
            Log::error('Firebase', $data);
            return false;
        }
    }
}
