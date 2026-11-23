<?php

namespace App\Helpers;

use stdClass;
use Exception;
use Kreait\Firebase;
use App\Enums\TopicsEnum;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use App\Traits\loggerExceptionTrait;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging;



class FcmHelper
{

    use loggerExceptionTrait;


    public $factory;
    public $messaging;
    public function __construct(Messaging $messaging)
    {
        /*Sending service account information to google firebase */
        $this->factory = (new Factory())
            ->withServiceAccount(config('firebase.projects.app.credentials.file'));
        /* Authenticate application using service account */
        $this->auth = $this->factory->createAuth();
        $this->messaging = $messaging;
}


    /**
     * create user on firebase and return user uuid
     *
     * @param  mixed $data
     * @return string
     */
    public function create(array $data):string
    {
        $user = array(
            'email' => $data['email'],
            'password' => $data['password']
        );
        try {
            /* type casting */
            $data = new stdClass();
            $createdUser = $this->auth->createUser($user);
            $data->uid = $createdUser->uid;
        } catch (Firebase\Exception\FirebaseException $e) {
            $this->saveExceptionLog($e,'Firebase create user on firebase');
            $data->uid = null;
        }
        return $data->uid;
    }


    /**
     * truncate :truncate the users collection/table from firebase
     *
     * @return bool
     */
    public function truncate():bool
    {

        try {
            $users = $this->auth->listUsers();

            foreach ($users as $user) {
                /** @var \Kreait\Firebase\Auth\UserRecord $user */
                $this->auth->deleteUser($user->uid);
            }
            return  true;
        } catch (Firebase\Exception\FirebaseException $e) {
            $this->saveExceptionLog($e,'Firebase truncate the users');
            return  false;
        }
    }

    /**
     * topic
     * send notification to a topic or all topics subscribed by user
     * @param  mixed $data
     * @param  mixed $title
     * @param  mixed $topic
     * @return bool
     */
    public function topic($data, $title, $topic):bool
    {
        try {
            if ($topic == 'all') {
                $message = CloudMessage::withTarget('topic', TopicsEnum::ANDROID)
                    ->withNotification(['title' => ucfirst($title), 'body' => $data]);
                $this->messaging->send($message);
                $message = CloudMessage::withTarget('topic', TopicsEnum::IOS)
                    ->withNotification(['title' => ucfirst($title), 'body' => $data]);
                $this->messaging->send($message);
                return true;
            }
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(['title' => ucfirst($title), 'body' => $data]);
            $this->messaging->send($message);
            return true;
        } catch (Exception $e) {
            $this->saveExceptionLog($e,'Firebase send notification to a topic');
            return false;
        }
    }
    /**
     * notifyToMultipleDevice send notifications to multiple devices using firebase tokens
     *
     * @param  array $data
     * @return bool
     */
    public function notifyToMultipleDevice(array $data)
    {

        $title = $data['title'] ?? null;
        $body = $data['body'] ?? null;
        $imageUrl = $data['imageUrl'] ?? null;
        $object= new stdClass();
        $notification = Notification::create($title, $body, $imageUrl);
        try {
            $message = CloudMessage::new();
            $message->withNotification($notification)
                ->withData($data['data'])->withDefaultSounds();
           $res=$this->messaging->sendMulticast($message, $data['tokens']);
           $response=$object->success=$res;
           return $response;
        } catch (Firebase\Exception\FirebaseException $e) {
            $this->saveExceptionLog($e,'notifyToMultipleDevice');
            return $e->getMessage();
        }
    }

    /**
     * notifyToSingleDevice send notifications to single device using firebase token
     *
     * @param  mixed $data
     * @return bool
     */
    public function notifyToSingleDevice(array $data)
    {
        $messaging = $this->factory->createMessaging();
        $title = $data['title'] ?? null;
        $body = $data['body'] ?? null;
        $imageUrl = $data['imageUrl'] ?? null;
        $notification = Notification::create($title, $body, $imageUrl);
        try {
            $message = CloudMessage::withTarget('token', $data['token'])
                ->withNotification($notification)
                ->withData($data['data'])->withDefaultSounds();
            $message = $messaging->send($message);
            Log::info('Firebase Notification', ['messageId' => $message, 'token' => $data['token'], 'data' => $data['data']]);
        } catch (Firebase\Exception\FirebaseException $e) {
            $this->saveExceptionLog($e,'Firebase notifyToSingleDevice');
            return $e->getMessage();
        }
    }




}
