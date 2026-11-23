<?php


namespace App\Services;

use App\Enums\OtpTypes;
use App\Enums\UserStatus;
use App\Http\Resources\User\UserResource;
use App\Mail\SendOtp;
use App\Models\FcmToken;
use App\Models\Otp;
use App\Models\User;
use App\Traits\loggerExceptionTrait;
use Facades\App\Services\FcmTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;

class OtpService
{
    use loggerExceptionTrait;

    protected $firebase;

    public function __construct()
    {
        $this->firebase = new FirebaseService();
    }

    public function validate($request)
    {
        $authType = $request->otpType;
        if ($authType == OtpTypes::SIGNIN_NUMBER_OTP) {
            return $this->signInOtp($request);
        }
        if ($authType == OtpTypes::SIGNUP_NUMBER_OTP) {
            return $this->signUpNumberOtp($request);
        }
        if ($authType == OtpTypes::SIGNUP_EMAIL_OTP) {
            return $this->SignUpEmailOtp($request);
        }
        if ($authType == OtpTypes::PASSWORD_RESET_EMAIL_OTP) {
            return $this->resetPasswordEmailOtp($request);
        }
        if ($authType == OtpTypes::PASSWORD_RESET_NUMBER_OTP) {
            return $this->resetPasswordNumberOtp($request);
        }

    }

    public function signInOtp($request)
    {
        try {
            $otpCode = Otp::where('email_phoneNumber', $request->phoneNumber)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid!']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::SIGNIN_NUMBER_OTP) {
                $userInfo = User::where('phoneNumber', $request->phoneNumber)->first();
                $otpCode->delete();
                if ($userInfo->status == UserStatus::BANNED) {
                    return response()->json(['response' => ['status' => false, 'message' => 'Your account is blocked. Kindly contact with admin support! ']], JsonResponse::HTTP_FORBIDDEN);
                }
                $sanctumToken = $userInfo->createToken($request->deviceName)->plainTextToken;
                $userInfo['token'] = $sanctumToken;
                FcmTokenService::addFcmtoken($request, $userInfo->id);
                $userInfo->setAttribute('firebaseCustomToken', $this->firebase->getToken($userInfo->uid));
                return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UserResource($userInfo)]]], JsonResponse::HTTP_OK);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'SignIn otp exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }

    }

    public function signUpNumberOtp($request)
    {
        try {
            $otpCode = Otp::where('email_phoneNumber', $request->phoneNumber)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid!']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::SIGNUP_NUMBER_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(['response' => ['status' => true, 'message' => 'Otp code is validated successfully!']], JsonResponse::HTTP_CREATED);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'SignUp number otp exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }


    }

    public function SignUpEmailOtp($request)
    {
        try {
            $otpCode = Otp::where('email_phoneNumber', $request->email)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid!']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::SIGNUP_EMAIL_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(['response' => ['status' => true, 'message' => 'Otp code is validated successfully!']], JsonResponse::HTTP_CREATED);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'SignUp Email otp exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);

        }


    }

    public function resetPasswordNumberOtp($request)
    {

        try {
            $otpCode = Otp::where('email_phoneNumber', $request->phoneNumber)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid!']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::PASSWORD_RESET_NUMBER_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(['response' => ['status' => true, 'message' => 'Otp code is validated successfully!']], JsonResponse::HTTP_CREATED);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Reset password number otp exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function resetPasswordEmailOtp($request)
    {

        try {
            $otpCode = Otp::where('email_phoneNumber', $request->email)->where('otp', $request->otp)->first();
            if (!$otpCode) {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp code is invalid!']], JsonResponse::HTTP_BAD_REQUEST);
            }
            if ($otpCode->otpType == OtpTypes::PASSWORD_RESET_EMAIL_OTP) {
                $otpCode->isVerified = 1;
                $otpCode->update();
                return response()->json(['response' => ['status' => true, 'message' => 'Otp code is validated successfully!']], JsonResponse::HTTP_CREATED);
            } else {
                return response()->json(['response' => ['status' => false, 'message' => 'Otp Type not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'Reset password Email otp exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
