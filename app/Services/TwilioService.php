<?php


namespace App\Services;

use App\Traits\loggerExceptionTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioService
{
    use loggerExceptionTrait;
    function __construct()
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $this->twilio_number = getenv("TWILIO_NUMBER");
        $this->client = new Client($account_sid, $auth_token);
    }

    public function sendMessage($message, $recipients)
    {
        try {
            $this->client->messages->create($recipients,
                ['from' => $this->twilio_number, 'body' => $message]);
            return ['status' => true];
        } catch (TwilioException $e) {
            $data = [
                'Time' => gmdate("F j, Y, g:i a"),
                'Status Code' => $e->getCode(),
                'Message' => $e->getMessage(),
            ];
            Log::error('Twillo', $data);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

}
