<?php


namespace App\Services;

use App\Traits\loggerExceptionTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrainTreeService
{
    use loggerExceptionTrait;

    public $gateway;

    public function __construct()
    {
        $this->gateway = new \Braintree\Gateway([
            'environment' => env('BTREE_ENVIRONMENT'),
            'merchantId' => env('BTREE_MERCHANT_ID'),
            'publicKey' => env('BTREE_PUBLIC_KEY'),
            'privateKey' => env('BTREE_PRIVATE_KEY')
        ]);
    }

    //create Client Token
    public function createClientToken($brainTreeCustomerId = null)
    {
        try {
            // pass $clientToken to your front-end / endpoint
            $clientToken = $this->gateway->clientToken()->generate([
                "customerId" => $brainTreeCustomerId
            ]);
            return $clientToken;
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Exception Create Client Token');
            return false;
        }

    }

    public function createPaymentMethod($nonceToken, $customerId)
    {
        try {
            return $this->gateway->paymentMethod()->create([
                'customerId' => $customerId,
                'paymentMethodNonce' => $nonceToken
            ]);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Exception Create Payment method');
            return false;
        }
    }
    public function createPaymentMethodNonce($paymentMethodToken)
    {
        try {
            return $this->gateway->paymentMethodNonce()->create($paymentMethodToken);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Exception Create Payment Method Nonce');
            return false;
        }

    }
    public function getPaymentMethod($token)
    {
        try {
            $paymentMethod = $this->gateway->paymentMethod()->find($token);
            return $paymentMethod->token;
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Exception find Payment method');
            return false;
        }
    }

    public function createCustomer($firstName, $lastName, $email, $phone)
    {
        try {
            $response = $this->gateway->customer()->create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'phone' => $phone,
            ]);
            return $response->customer->id;
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Exception Create Customer Braintree');
            return false;
        }
    }

    public function createTransaction($nonceToken, $amount)
    {
        try {
            return $this->gateway->transaction()->sale([
                'amount' => $amount,
                'paymentMethodNonce' => $nonceToken,
                'options' => [
                    'submitForSettlement' => True
                ]
            ]);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Exception Create Transaction');
            return false;
        }

    }

    public function addCreditCard($customerId){
        try {
            return $this->gateway->creditCard()->create([
                'customerId' => $customerId,
                'number' => '4111111111111111',
                'expirationDate' => '06/25',
                'cvv' => '100'
            ]);

        }catch (\Exception $e){
            $this->saveExceptionLog($e, 'Exception find Payment method');
            return false;
        }
    }

}
