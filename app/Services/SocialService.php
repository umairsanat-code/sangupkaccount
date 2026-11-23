<?php

namespace App\Services;

use App\Enums\OtpTypes;
use App\Http\Resources\User\AppleResource;
use App\Models\Otp;
use Exception;
use App\Models\User;
use App\Enums\SocialSite;
use App\Enums\UserStatus;
use App\Models\SocialLogin;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Traits\loggerExceptionTrait;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\SocialResource;

/**
 * SocialService handle all social login related logic
 */
class SocialService
{
    use loggerExceptionTrait;

    /**
     * facebook represent intance of facebook service
     *
     * @var mixed
     */
    protected $facebook;
    /**
     * gmail  represent intance of gmail service
     *
     * @var mixed
     */
    protected $gmail;
    /**
     * apple represent intance of apple service
     *
     * @var mixed
     */
    protected $apple;
    /**
     * provider identify which social site we are going to use like facebook , gmail , apple
     *
     * @var mixed
     */
    protected $provider;

    /**
     * request instance of App\Http\Requests\Api\User\SocialRequest;
     *
     * @var mixed
     */
    protected $request;
    /**
     * response
     *
     * @var mixed
     */
    protected $responseData;
    /**
     * device name from request
     *
     * @var mixed
     */
    protected $deviceName;
    /**
     * used to store firebase instance
     *
     * @var mixed
     */
    protected $firebase;
    /**
     * used to store firebase instance
     *
     * @var mixed
     */
    protected $fcm;
    /**
     * user
     *
     * @var mixed
     */
    protected $user;
    protected $token;

    public function __construct()
    {
        $this->facebook = new FacebookService();
        $this->gmail = new GmailService();
        $this->user = new UserRepository();
        $this->firebase = new FirebaseService();
        $this->fcm = new FcmTokenService();
    }

    public function with($data)
    {
        $this->request = $data;
        $this->provider = $data->socialSite;
        $this->token = $data->accessToken;
        $this->deviceName = $data->deviceName;
        return $this;
    }



    public function get()
    {

        try {
            DB::beginTransaction();
            $userData = '';
            /* handle facebook */
            if ($this->provider == SocialSite::FACEBOOK) {
                $userData = $this->facebook->getToken($this->token);
//                $userData = $this->facebook->validateAccessToken($this->token);
                if (!$userData) {
                    return response()->json(['response' => ['status' => false, 'message' => 'Unable to get the user data from facebook, please try again!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                /* if user already signin through facebook then return jwt */
                $user = $this->user->getByFbSocialId($userData['socialId']);
                if (!is_null($user)) {
                    if($this->isBlockedUser($user->status)){
                        return response()->json(['response' => ['status' => false, 'message' => 'Your account is blocked. Kindly contact with admin support! ']], JsonResponse::HTTP_FORBIDDEN);
                    }
                    $token = $user->createToken($this->deviceName)->plainTextToken;
                    $user['token'] = $token;
                    $this->fcm->addFcmtoken($this->request, $user->id);
                    $user->setAttribute('firebaseCustomToken', $this->firebase->getToken($user->uid));
                    DB::commit();
                    return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UserResource($user)]]], JsonResponse::HTTP_OK);
                }
               if (isset($userData['email'])){
                   $otpExists=Otp::where('email_phoneNumber',$userData['email'])->first();

                   if (!$otpExists){
                       $dataEmail=$userData['email'];
                       $this->otpCode($dataEmail);
                   }else{
                       $otpExists->update([
                           'isVerified'=>true
                       ]);
                   }

               }
                SocialLogin::create(['type' => SocialSite::FACEBOOK, 'socialId' => $userData['socialId']]);
                DB::commit();
                return response()->json(['response' => ['status' => false, 'data' => ['auth' => $this->formattedData($userData)]]], JsonResponse::HTTP_OK);
            }
            /* handle gmail */
            if ($this->provider == SocialSite::GMAIL) {
                $data = $this->gmail->getInfoByToken($this->token);

                if (!$data) {
                    return response()->json(['response' => ['status' => false, 'message' => 'Unable to get the user data from google, please try again!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
                /* if user already signin through gmail then return jwt */
                $user = $this->user->getByGmailSocialId($data['socialId']);
                if (!is_null($user)) {
                    if($this->isBlockedUser($user->status)){
                        return response()->json(['response' => ['status' => false, 'message' => 'Your account is blocked. Kindly contact with admin support! ']], JsonResponse::HTTP_FORBIDDEN);
                    }
                    $token = $user->createToken($this->deviceName)->plainTextToken;
                    $user['token'] = $token;
                    $this->fcm->addFcmtoken($this->request, $user->id);
                    //email or number  save  otp table and verified true this location
                    $user->setAttribute('firebaseCustomToken', $this->firebase->getToken($user->uid));
                    DB::commit();
                    return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UserResource($user)]]], JsonResponse::HTTP_OK);
                }
                if (isset($data['email'])){
                    $otpExists=Otp::where('email_phoneNumber',$data['email'])->first();
                    if (!$otpExists){
                        $dataEmail=$data['email'];
                        $this->otpCode($dataEmail);
                    }else{
                        $otpExists->update([
                            'isVerified'=>true
                        ]);
                    }
                }
                SocialLogin::create(['type' => SocialSite::GMAIL, 'socialId' => $data['socialId']]);
                DB::commit();
                return response()->json(['response' => ['status' => false, 'data' => ['auth' => $this->formattedData($data)]]], JsonResponse::HTTP_OK);
            }
            /* handle apple */
            if ($this->provider == SocialSite::APPLE) {
                $userInfo['socialSite'] = SocialSite::APPLE;
                $userInfo['id'] = $this->request->accessToken;
                $userInfo['firstName'] =$this->request->firstName;
                $userInfo['lastName'] = $this->request->lastName;
                $userInfo['email'] = $this->request->email;
                if ($userInfo) {
                    $user = User::where('appleId', $this->request->userIdentifier)->first();
                    if ($user) {
                        if ($user->status == UserStatus::BLOCK) {
                            return response()->json(['response' => ['status' => false, 'message' => 'Your account is blocked. Kindly contact with admin support! ']], JsonResponse::HTTP_FORBIDDEN);
                        }
                        $token = $user->createToken($this->deviceName)->plainTextToken;
                        $user['token'] = $token;
                        $this->fcm->addFcmtoken($this->request, $user->id);
                        $user->setAttribute('firebaseCustomToken', $this->firebase->getToken($user->uid));
                        DB::commit();
                        return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UserResource($user)]]], JsonResponse::HTTP_OK);
                    }
                    $userEmail = User::where('email', $this->request->email)->first();
                    if ($userEmail) {
                        return response()->json(['response' => ['status' => false, 'message' => "Sorry! You can't login as your account is already created "]], JsonResponse::HTTP_FORBIDDEN);

                    }
                    SocialLogin::create(['type' =>SocialSite::APPLE , 'socialId' => $this->request->userIdentifier]);
                    $userInfo['userIdentifier'] = $this->request->userIdentifier;
                    DB::commit();
                    return response()->json(['response' => ['status' => false, 'data' => ['auth' => new AppleResource($userInfo)]]], JsonResponse::HTTP_OK);

//                    $token = $user->createToken($this->deviceName)->plainTextToken;
//                    $user['token'] = $token;
//                    $this->fcm->addFcmtoken($this->request, $user->id);
//                    $user->setAttribute('firebaseCustomToken', $this->firebase->getToken($user->uid));
//                    DB::commit();
//                    return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UserResource($user)]]], JsonResponse::HTTP_OK);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'Social Service exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Soemthing went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    private function isBlockedUser($status){
        if ($status== UserStatus::BANNED) {
          return  true;
        }
        return false;
    }
    public function formattedData($data)
    {
        $data['avatar'] = null;
        $data['phoneNumber'] = null;
        return $data;
    }
    public function otpCode($dataEmail){
        $otp = mt_rand(10000, 99999);
        Otp::create([
            'email_phoneNumber'=>$dataEmail,
            'otp'=>$otp,
            'isVerified'=>true,
            'otpType'=>OtpTypes::SIGNUP_EMAIL_OTP,
        ]);
    }
}
