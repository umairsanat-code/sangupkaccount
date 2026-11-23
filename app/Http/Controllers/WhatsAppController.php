<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function handleWebhook(Request $request)
    {
        \Log::info('Webhook received', $request->all());

        try {
            $data = $request->all();

            // Extract necessary data from the request
            $from = $data['From'];
            $body = $data['Body'];

            // Process the message and determine the response
            $response = $this->processMessage($body);

            // Send the response back to the user
            $this->sendMessage($from, $response);

            \Log::info('Response sent', ['to' => $from, 'message' => $response]);
        } catch (\Exception $e) {
            \Log::error('Error handling webhook', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong'.$e->getMessage()], 500);
        }
    }

    private function processMessage($message)
    {
        // Implement your message processing logic here
        $session = session()->get('conversation_state', []);

        if (empty($session)) {
            session()->put('conversation_state', ['step' => 'initial']);
            return "Do you want a battery or a car wash? Choose one.";
        }

        switch ($session['step']) {
            case 'initial':
                if (strtolower($message) == 'battery') {
                    session()->put('conversation_state', ['step' => 'battery_size']);
                    return "What size do you want?";
                } elseif (strtolower($message) == 'car wash') {
                    session()->put('conversation_state', ['step' => 'car_wash_type']);
                    return "What type of car wash do you want?";
                } else {
                    return "Please choose 'battery' or 'car wash'.";
                }
                break;

            case 'battery_size':
                session()->put('conversation_state', ['step' => 'complete', 'battery_size' => $message]);
                // Save the order details to the database or perform other actions
                return "Thank you! Your battery size is noted.";
                break;

            case 'car_wash_type':
                session()->put('conversation_state', ['step' => 'complete', 'car_wash_type' => $message]);
                // Save the order details to the database or perform other actions
                return "Thank you! Your car wash type is noted.";
                break;

            case 'complete':
                session()->forget('conversation_state');
                return "Your order has already been registered.";
                break;

            default:
                return "I'm not sure what you mean. Please start again.";
        }
    }

    private function sendMessage($to, $message)
    {
        $client = new Client(env('Twilio_sid'),env('twilio_token'));
//        new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
        // dd($client);
        $client->messages->create($to, [
            'from' => 'whatsapp:' . env('TWILIO_WHATSAPP_NUMBER'),
            'body' => $message,
        ]);
    }
}



