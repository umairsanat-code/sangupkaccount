<?php


namespace App\Services;

use App\Enums\OtpTypes;
use App\Enums\UserStatus;
use App\Mail\ResetPassword;
use App\Mail\SendOtp;
use App\Models\Otp;
use App\Models\User;
use App\Traits\loggerExceptionTrait;
use Doctrine\DBAL\Driver\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendOtpService
{
    use loggerExceptionTrait;

    protected $twilio;

    public function __construct()
    {
        $this->twilio = new TwilioService();
    }

    public function sendOtp($request)
    {
        $authType = $request->otpType;
        if ($authType == OtpTypes::SIGNUP_EMAIL_OTP) {
            return $this->sendSignUpEmailOtp($request);
        }
        if ($authType == OtpTypes::SIGNIN_NUMBER_OTP) {
            return $this->sendSignInOtp($request);
        }
        if ($authType == OtpTypes::PASSWORD_RESET_NUMBER_OTP) {
            return $this->sendResetPasswordNumberOtp($request);
        }
        if ($authType == OtpTypes::PASSWORD_RESET_EMAIL_OTP) {
            return $this->sendResetPasswordEmailOtp($request);
        }
        if ($authType == OtpTypes::SIGNUP_NUMBER_OTP) {
            return $this->sendSignUpNumberOtp($request);
        }

    }

    public function sendSignUpEmailOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $email = $request->email;
            $user = User::where('email', $request->email)->first();
            if (!is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'User already exist!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $otpVerfiy = Otp::where('email_phoneNumber', $email)->first();
            if (!is_null($otpVerfiy)) {
                try {
                    DB::beginTransaction();
                    Mail::to($email)->send(new SendOtp($otp));
                    $otpVerfiy->update([
                        'otp' => $otp,
                        'otpType' => OtpTypes::SIGNUP_EMAIL_OTP,
                    ]);
                    DB::commit();
                    return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                } catch (\Exception $e) {
                    return response()->json(['response' => ['status' => false, 'message' => $e->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);
                }

            }
            Otp::create([
                'email_phoneNumber' => $request->email,
                'otp' => $otp,
                'otpType' => OtpTypes::SIGNUP_EMAIL_OTP,
            ]);
            try {
                Mail::to($email)->send(new SendOtp($otp));
            } catch (\Exception $e) {
                return response()->json(['response' => ['status' => false, 'message' => $e->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);
            }
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Send signUp Email exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);

        }
    }

    public function sendResetPasswordEmailOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $user = User::where('email', $request->email)->first();
            if (is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'Unable to find user with given email!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $email = $request->email;
            $otpVerfiy = Otp::where('email_phoneNumber', $email)->first();
            if (!is_null($otpVerfiy)) {
                DB::beginTransaction();
                Mail::to($email)->send(new ResetPassword($otp, $user));
                $otpVerfiy->update([
                    'otp' => $otp,
                    'otpType' => OtpTypes::PASSWORD_RESET_EMAIL_OTP,
                ]);
                DB::commit();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            }
            Otp::create([
                'email_phoneNumber' => $request->email,
                'otp' => $otp,
                'otpType' => OtpTypes::PASSWORD_RESET_EMAIL_OTP,
            ]);
            Mail::to($email)->send(new ResetPassword($otp, $user));
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, OtpTypes::PASSWORD_RESET_EMAIL_OTP . ' exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    public function sendSignInOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $phoneNumber = $request->phoneNumber;
            $user = User::where('phoneNumber', $phoneNumber)->first();
            if (is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'Unable to find user with given phoneNumber!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($user->status == UserStatus::BANNED) {
                return response()->json(['response' => ['status' => false, 'message' => 'Your account is blocked. Kindly contact with admin support! ']], JsonResponse::HTTP_FORBIDDEN);
            }
            $otpVerfiy = Otp::where('email_phoneNumber', $phoneNumber)->first();
            if (!is_null($otpVerfiy)) {
                if ($otpVerfiy->isVerified == 0) {
                    DB::beginTransaction();
                    $message = $otp . " is your eLoumo verification code.";
                    $msg = $this->twilio->sendMessage($message, $phoneNumber);
                    if ($msg['status']) {
                        $otpVerfiy->update([
                            'otp' => $otp,
                            'otpType' => OtpTypes::SIGNIN_NUMBER_OTP,
                        ]);
                    } else {
                        return response()->json(['response' => ['status' => false, 'message' => $msg['message']]], JsonResponse::HTTP_BAD_REQUEST);
                    }
                    DB::commit();
                    return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                } else {
                    $message = $otp . " is your eLoumo verification code.";
                    $msg = $this->twilio->sendMessage($message, $phoneNumber);
                    if ($msg['status']) {
                        $otpVerfiy->update([
                            'otp' => $otp,
                            "isVerified"=>false,
                            'otpType' => OtpTypes::SIGNIN_NUMBER_OTP,
                        ]);
                    }else {
                        return response()->json(['response' => ['status' => false, 'message' => $msg['message']]], JsonResponse::HTTP_BAD_REQUEST);
                    }
                    return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                }
            }
            $message = $otp . " is your eLoumo verification code.";
            $msg = $this->twilio->sendMessage($message, $phoneNumber);
            if ($msg['status']) {
                Otp::create([
                    'email_phoneNumber' => $phoneNumber,
                    'otp' => $otp,
                    'otpType' => OtpTypes::SIGNIN_NUMBER_OTP,
                ]);

            } else {
                return response()->json(['response' => ['status' => false, 'message' => $msg['message']]], JsonResponse::HTTP_BAD_REQUEST);
            }
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Send signIn number otp verified exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);

        }

    }

    public function sendResetPasswordNumberOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $phoneNumber = $request->phoneNumber;
            $user = User::where('phoneNumber', $phoneNumber)->first();
            if (is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'Unable to find user with given phone number!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($user->status == UserStatus::BANNED) {
                return response()->json(['response' => ['status' => false, 'message' => 'Your account is blocked. Kindly contact with admin support! ']], JsonResponse::HTTP_FORBIDDEN);
            }
            $message = $otp . " is your eLoumo verification code.";
            $otpVerfiy = Otp::where('email_phoneNumber', $phoneNumber)->first();
            if (!is_null($otpVerfiy)) {
                DB::beginTransaction();
                $msg = $this->twilio->sendMessage($message, $phoneNumber);
                if ($msg['status']) {
                    $otpVerfiy->update([
                        'otp' => $otp,
                        'otpType' => OtpTypes::PASSWORD_RESET_NUMBER_OTP,
                    ]);
                } else {
                    return response()->json(['response' => ['status' => false, 'message' => $msg['message']]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                DB::commit();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            }
            $msg = $this->twilio->sendMessage($message, $phoneNumber);
            if ($msg['status']) {
                Otp::create([
                    'email_phoneNumber' => $phoneNumber,
                    'otp' => $otp,
                    'otpType' => OtpTypes::PASSWORD_RESET_NUMBER_OTP,
                ]);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => $msg['message']]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Send password number otp verified exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    public function sendSignUpNumberOtp($request)
    {
        try {
            $otp = $this->generateOtp($request);
            $phoneNumber = $request->phoneNumber;
            $user = User::where('phoneNumber', $request->phoneNumber)->first();
            if (!is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => 'User already exist!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $otpVerfiy = Otp::where('email_phoneNumber', $phoneNumber)->first();
            if (!is_null($otpVerfiy)) {
                DB::beginTransaction();
                $message = $otp . " is your eLoumo verification code.";
                $msg = $this->twilio->sendMessage($message, $phoneNumber);
                if ($msg['status']) {
                    $otpVerfiy->update([
                        'otp' => $otp,
                        'otpType' => OtpTypes::SIGNUP_NUMBER_OTP,
                    ]);
                } else {
                    return response()->json(['response' => ['status' => false, 'message' => $msg['message']]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                DB::commit();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            }
            $message = $otp . " is your eLoumo verification code.";
            $msg = $this->twilio->sendMessage($message, $phoneNumber);
            if ($msg['status']) {
                Otp::create([
                    'email_phoneNumber' => $phoneNumber,
                    'otp' => $otp,
                    'otpType' => OtpTypes::SIGNUP_NUMBER_OTP,
                ]);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => $msg['message']]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Exceptions signUp send otp');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    public function generateOtp($request)
    {
        $otp =mt_rand(100000, 999999);
        if (env('APP_ENV') == 'staging' || env('APP_ENV') == 'local'){
            $otp = 111111;
        }
        return $otp;
    }
}
