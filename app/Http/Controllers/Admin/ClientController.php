<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class ClientController extends Controller
{
    public function index(){
        $user = Auth::user();

        // Check if the authenticated user is an admin
        if ($user->hasRole('admin')) {
            // If the user is an admin, show all clients
            $clients = Client::where('status','active')->with('creator')->get();
        } else {
            // If the user is not an admin, show clients associated with the user
            $clients = Client::where('user_id', $user->id)->where('status','active')->with('creator')->get();
        }
        if(\request()->ajax()){
            return DataTables::of($clients)
                ->addColumn('creator', function($client) {

                        return $client->creator ? $client->creator->name : 'N/A';

                })
                ->addColumn('action', function($client) {
                    return '<a href="#" class="btn btn-sm btn-primary edit-client" data-id="'.$client->id.'">Editing</a>
                       <button type="button" class="btn btn-sm btn-info sms-btn" data-client-id="'. $client->id.'">Inbox</button>
                        ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.clients.listing');

    }
    public function store(Request $request)
    {
        // Validate the form data


        $request->validate([
            'firstName' => 'required|string',
            'email' => 'required|email|unique:clients,email',
            'phoneNumber' => 'required|string',
            // Add more validation rules as needed
        ]);
        // Create a new client
        $client = new Client();
        $client->name = $request->firstName;
        $client->user_id = Auth::id();
        $client->phoneNumber = $request->phoneNumber;
        $client->email = $request->email;
//        $client->email = $request->email;
        // Add more fields as needed
        $client->save();

        // Redirect with a success message
        return response()->json(['success' => true]);
    }
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }
    public function update(Request $request, $id)
    {

        try {
//            dd($request->all());
            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:clients,email,' . $id,
                'phoneNumber' => 'required|unique:clients,phoneNumber,' . $id,
                // Add more validation rules as needed
            ]);

            // Update the client
           $client=Client::where('id',$id)->first();
           $client->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'phoneNumber'=>$client->phoneNumber
           ]);

            return response()->json(['success' => 'Client updated successfully.']);
        } catch (ValidationException $e) {

            // Validation failed, return validation errors
            return response()->json(['errors' => $e->validator->getMessageBag()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'An error occurred while updating the client.'], 500);
        }
    }
     // Assuming you have a Message model

    public function sendSms(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();
//dd($request->all());
        // Find the client based on the client_id
        $client = Client::findOrFail($request->client_id);
//dd(env('TELNYX_PHONE_NUMBER'));
//        $user=Auth::user();
        // Initialize Telnyx message parameters
        $data = array(
            "from" => env('TELNYX_PHONE_NUMBER'), // Your Telnyx phone number
//            "to" => $client->phoneNumber, // Client's phone number
            "to" => '+18885973435', // Client's phone number
//            "text" => $request->message,
            "text" => 'Testing sms developer',
            "media_urls" => [] // Empty array for media URLs
        );

        // Convert data to JSON
//        $data_string = json_encode($data);

        // Initialize cURL session
        $data_string = json_encode($data);
//dd($data_string);
        // Initialize cURL session
        $ch = curl_init('https://api.telnyx.com/v2/messages');

        // Set cURL options
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . env('TELNYX_API_KEY') // Your Telnyx API key
        ));

        // Execute cURL request
        $result = curl_exec($ch);

        // Check for errors

        // Check for errors
        if(curl_errno($ch)) {
            // Handle cURL error
            $error_msg = curl_error($ch);
            return redirect()->back()->with('error', 'Failed to send SMS: ' . $error_msg);
        }

        // Close cURL session
        curl_close($ch);

        // Handle Telnyx API response

        // Check if response is valid JSON and contains 'data' key
//        if (json_last_error() !== JSON_ERROR_NONE || !isset($response['data'])) {
//            return redirect()->back()->with('error', 'Failed to send SMS: Invalid response from Telnyx API');
//        }
        $response = json_decode($result, true);

// Check if response contains the necessary data
        if (!isset($response['data'])) {
            return redirect()->back()->with('error', 'Failed to send SMS: Invalid response from Telnyx API');
        }

// Extract relevant data from the response
        $data = $response['data'];

// Create a new message record
        $message = new Message();
        $message->user_id = Auth::id();
        $message->client_id = $client->id;
        $message->is_read = false;
        $message->message = $data['text'];
        $message->status = 'send'; // Use the status from the response
        $message->message_date = now()->toDateString();
        $message->message_time = now()->toTimeString();

// Check if the message status is 'queued'
        if ($data['to'][0]['status'] === 'queued') {

            // Save the message to the database
            $message->save();

            return redirect()->back()->with('success', 'SMS sent successfully!');
        } else {
//            dd('not queued');

            return redirect()->back()->with('error', 'Failed to send SMS: Unexpected status from Telnyx API');
        }

    }

    public function handleWebhook(Request $request)
    {
        // Validate request signature
        $sigHeader = $request->header('telnyx-signature-ed25519');
        $timestamp = $request->header('telnyx-timestamp');

        $body = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $timestamp . $body, 'YOUR_API_SECRET_KEY');

        if (!hash_equals($sigHeader, $expectedSignature)) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Request signature is valid, process the webhook
        $data = json_decode($body, true);

        // Extract relevant data from the webhook payload
        $from = $data['data']['payload']['from']['phone_number'];
        $body = $data['data']['payload']['body'];

        // Process the incoming SMS
        // For example, log it or trigger some action
        \Log::info("Received SMS from $from: $body");

        return response()->json(['success' => true]);
    }
    public function receiveSMS(Request $request)
    {
        // Log the incoming request for debugging purposes
        Log::info('Incoming SMS:', $request->all());

        // Extract data from the request
        $data = $request->input('data');
        $payload = $data['payload'];

//        $from = '+13177999294';
        $from = $payload['from'];
        $to = $payload['to'];
//        $to = '+18885973435';
        $text = $payload['text'];

        // Find the client by phone number (assuming you have a Client model)
        $client = Client::where('phone', $from)->first();

        $client = true;

        if ($client) {
            // Create a new message record
            $message = new Message();
            $message->user_id = Auth::id(); // Assuming the user is authenticated
            $message->client_id = 2;
            $message->is_read = false;
            $message->message = $text;
            $message->status = 'receive'; // Set the status to 'receive'
            $message->message_date = now()->toDateString();
            $message->message_time = now()->toTimeString();
            $message->save();

            return response()->json(['status' => 'success'], 200);
        } else {
            return response()->json(['error' => 'Client not found'], 404);
        }
    }
    public function loadPreviousChats(Request $request)
    {
        $clientId = $request->input('client_id');
        $user = Auth::user();

        $previousChats = Message::where('client_id', $clientId)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id) // Sent messages by the user
                ->whereIn('status', ['sent','received']);
            })
//            ->orWhere(function ($query) use ($user) {
//                $query->where('user_id', '<>', $user->id) // Received messages by the user from the client
//                ->where('status', '');
//            })
            ->with('user', 'client')->get();
//dd($previousChats->toArray());
// Log the retrieved chat history

        $previousChats = $previousChats->map(function ($chat) use ($user) {
            // Determine the position of the message based on the sender's user ID
            $position = ($chat->user_id === $user->id && $chat->status === 'sent') ? 'right' : 'left';
            if ($position=="right"){
                $user_name=$chat->user->firstName;
            }else{
                $user_name=$chat->client->name;

            }
            $dateString = Carbon::parse($chat->created_at)->format('Y-m-d H:i A');
//            $dateString = $chat->created_at->formatLocalized('%e %b %l:%M %P');


            return [
                'user_name' => $user_name,
                'created_at' => $dateString,
                'message' => $chat->message,
                'position' => $position
            ];
        });

// Log the mapped chat history

        return response()->json($previousChats);




        return response()->json($previousChats);
    }

}
