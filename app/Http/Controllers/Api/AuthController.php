<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Http\Requests\Api\User\UserBlockRequest;
use App\Http\Resources\OtherUserProfile\ActiveProductResource;
use App\Http\Resources\OtherUserProfile\OtherUserProfileResource;
use App\Http\Resources\OtherUserProfile\SoldProductResource;
use App\Http\Resources\OtherUserProfile\UserReviewsResource;
use App\Http\Resources\User\UpdateProfileResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Otp;
use App\Models\Product;
use App\Models\User;
use App\Models\FcmToken;
use App\Enums\SocialSite;
use App\Enums\UserStatus;
use App\Models\UserBlock;
use App\Traits\BlockedUserProductDeActiveTrait;
use Illuminate\Support\Str;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Enums\SignInTypeEnum;
use App\Traits\FileUploadTrait;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\loggerExceptionTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Facades\App\Services\SocialService;
use App\Http\Resources\User\UserResource;
use Facades\App\Services\FcmTokenService;
use App\Http\Requests\RegistrationRequest;
use Facades\App\Repository\UserRepository;
use App\Http\Resources\User\ProfileResource;
use App\Http\Requests\Api\User\logoutRequest;
use App\Http\Requests\Api\User\SigninRequest;
use App\Http\Requests\Api\User\SocialRequest;
use App\Models\SocialLogin;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    use FileUploadTrait, loggerExceptionTrait,BlockedUserProductDeActiveTrait;

    protected $imageDirectory = "uploads/users";

    public function socialSignin(SocialRequest $socialRequest)
    {
        return SocialService::with($socialRequest)->get();
    }

    public function signUp(RegistrationRequest $request, FirebaseService $firebase)
    {

        try {
            DB::beginTransaction();
            $user = $request->only('userName', 'password', 'email', 'phoneNumber', 'avatar');
            $name = explode(" ", $user['userName']);
            $user['firstName'] = $name[0];
            $user['lastName'] = null;
            if(str_replace($user['firstName'],"",$user['userName'])){
                $user['lastName'] = ltrim(str_replace($user['firstName'],"",$user['userName']));
            }
            $socialType = $request->socialSite;
            if ($socialType == SocialSite::APPLE) {
                $user['password'] = '';
                $firebasePwd=bcrypt('TechSwivel#001');
            }else {
                $user['password'] = bcrypt($user['password']);
                $firebasePwd = bcrypt($user['password']);
                $email = $user['email'];
                if ($email) {
                    $otpEmail = Otp::where('email_phoneNumber', $email)->where('isVerified', 1)->first();
                    if (is_null($otpEmail)) {
                        return response()->json(['response' => ['status' => false, 'message' => 'Email  is not verified!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                    } else {
                        $otpEmail->delete();
                    }
                }
                $phoneNumber = $user['phoneNumber'];
                if ($phoneNumber) {
                    $otpNumber = Otp::where('email_phoneNumber', $phoneNumber)->where('isVerified', 1)->first();
                    if (is_null($otpNumber)) {
                        return response()->json(['response' => ['status' => false, 'message' => 'Phone Number is not verified!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                    } else {
                        $otpNumber->delete();
                    }
                }
            }
            if ($request->has('avatar')) {
                $user['avatar'] = $this->uploadFile($request->avatar, $this->imageDirectory);
            }
            if ($request->has('socialId')) {
                $socialData = SocialLogin::where('socialId', $request->socialId)->first();
                if ($socialData->type == SocialSite::FACEBOOK) {
                    $user['fbId'] = $socialData->socialId;
                }
                if ($socialData->type == SocialSite::GMAIL) {
                    $user['googleId'] = $socialData->socialId;
                }
                if ($socialData->type == SocialSite::APPLE) {
                    $user['appleId'] = $socialData->socialId;
                }

                $socialData->delete();
            }

            $firebaseUser = $firebase->createUserAccount(['email' => $request->email, 'password' => $firebasePwd]);
            if (!$firebaseUser->status) {
                return response()->json(['response' => ['status' => false, 'message' => $firebaseUser->message]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            $user['uid'] = $firebaseUser->data->uid;
            $user = User::create($user);
            $token = $user->createToken($request->deviceName)->plainTextToken;
            $user['token'] = $token;
            FcmTokenService::addFcmtoken($request, $user->id);
            $user->setAttribute('firebaseCustomToken', $firebase->getToken($user->uid));
            /* assign role as a buyer  */
            $user->assignRole('buyer');
            DB::commit();
            return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UserResource($user)]]], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'SignUp exception');
            return response()->json(['response' => ['status' => false, 'message' =>'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function signIn(SigninRequest $request, FirebaseService $firebase)
    {
        try {
            DB::beginTransaction();
            $user = null;
            $message = 'Unable to find user with given email!';
                $user = User::with('sellers')->where('email', $request->email)->first();
            if (is_null($user)) {
                return response()->json(['response' => ['status' => false, 'message' => $message]], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($user->status == UserStatus::BANNED) {
                return response()->json(['response' => ['status' => false, 'message' => 'Your account is blocked. Kindly contact with admin support! ']], JsonResponse::HTTP_FORBIDDEN);
            }
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['response' => ['status' => false, 'message' => 'Your password is wrong!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            /* generating token by usign Sanctum */
            $token = $user->createToken($request->deviceName)->plainTextToken;
            $user['token'] = $token;
            FcmTokenService::addFcmtoken($request, $user->id);
            $user->setAttribute('firebaseCustomToken', $firebase->getToken($user->uid));
            DB::commit();
            return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UserResource($user)]]], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'SignIn error exception');
            return response()->json(['response' => ['status' => false, 'data' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function logout(logoutRequest $request)
    {
        try {
            $user = request()->user();
            if (!is_null($user)) {
                $deviceIdentifier=FcmToken::where('userId', $user->id)->where('deviceidentifier', $request->deviceIdentifier)->first();
                if ($deviceIdentifier){
                    $deviceIdentifier->delete();
                    $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
                    return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                }else{
                    return response()->json(['response' => ['status' => false, 'message' => 'DeviceIdentifier not correct!']], JsonResponse::HTTP_BAD_REQUEST);
                }
            }
            return response()->json(['response' => ['status' => false, 'message' => 'Unable to find sanctum token against requested user!']], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->saveExceptionLog($e, 'logout error exception');
            return response()->json(['response' => ['status' => false, 'message' => 'Something went wrong!']], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function profile(Request $request)
    {
        if($request->userId!=false){
        $user = User::find($request->userId);
        if ($user){
            $activeProduct=Product::with('imageProducts')->where('sellerId',$user->id)->where('isSellerBlocked',0)->get();
            $saleProduct = Product::query()
                ->where('salePrice', '!=', 0)
                ->where('isSellerBlocked',0)
                ->where('sellerId', '=', $user->id)
                ->where('inStockQuantity', '!=', 0)
                ->join('order_items', 'order_items.productId', '=', 'products.id')
                ->selectRaw('products.*, SUM(order_items.quantity) AS quantity')
                ->groupBy(['products.id']) // should group by primary key
                ->orderByDesc('quantity')
                ->limit(5)
                ->with('imageProducts')->get();
            $userReviews=Order::with('seller','buyer')->where('sellerId',$user->id)
                ->where(function ($q) {
                    $q->whereIn('status', [OrderStatus::COMPLETED, OrderStatus::CANCELED]);
                })->get()->unique('buyerId');
            return response()->json(['response' => ['status' => true, 'data' => ['userDetail' => new OtherUserProfileResource($user),'activeProducts'=>ActiveProductResource::collection($activeProduct),'soldProducts'=>SoldProductResource::collection($saleProduct),'userReviews'=>UserReviewsResource::collection($userReviews)]]], JsonResponse::HTTP_OK);

        }else{
            return response()->json(['response' => ['status' => false, 'message' => 'User id is not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        }else{
            $user = Auth::user();
            return response()->json(['response' => ['status' => true, 'data' => ['auth' => new ProfileResource($user)]]], JsonResponse::HTTP_OK);
        }

    }
    public function profileUpdate(UpdateProfileRequest $request){
        try {
            $user=User::where('id',Auth::id())->first();
            $name = explode(" ", $request['userName']);
            $userName['firstName'] = $name[0];
            $userName['lastName'] = null;
            if(str_replace($userName['firstName'],"",$request['userName'])){
                $userName['lastName'] = ltrim(str_replace($userName['firstName'],"",$request['userName']));
            }
            $lastName=$userName['lastName'];
            if($userName['lastName']==$request['userName']){
                $lastName=$user->lastName;
            }
            $avatar=null;
            if ($request->has('avatar')) {
                $this->deleteFile($user->avatar);
                $avatar = $this->uploadFile($request->avatar, $this->imageDirectory);
            }
            $user->update([
                "firstName"=> $userName['firstName']?$userName['firstName']:$user->firstName,
                "lastName"=> $lastName,
                "avatar"  => $avatar?$avatar:$user->avatar,
            ]);
            return response()->json(['response' => ['status' => true, 'data' => ['auth' => new UpdateProfileResource($user)]]], JsonResponse::HTTP_CREATED);

        }catch (\Exception $e){
            $this->saveExceptionLog($e, 'Update profile error exception');
            return response()->json(['response' => ['status' => false, 'message' => $e->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);

        }
    }
    public function userBlock(UserBlockRequest $request){
        try {
            DB::beginTransaction();
            if ($request->status==UserStatus::BLOCK){
                $isBanned = User::where('id',$request->userId)->where('status','!=',UserStatus::BANNED)->first();
                if ($isBanned->id==Auth::id()){
                    return response()->json(['response' => ['status' => false, 'message' =>'Sorry! You cannot block yourself!']], JsonResponse::HTTP_BAD_REQUEST);

                }
                if ($isBanned) {
                $isBlock = UserBlock::where('blockedUserId',$isBanned->id)->where('blockedBy',Auth::id())->first();
               if ($isBlock==null){
                UserBlock::create([
                    'blockedBy'=>Auth::id(),
                    'blockedUserId'=>$request->userId,
                    'status'=>UserStatus::BLOCK
                ]);
               }
                DB::commit();
                return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
            }
            }elseif ($request->status==UserStatus::UNBLOCK){
                $isBanned = User::where('id',$request->userId)->where('status','!=',UserStatus::BANNED)->first();
                if ($isBanned) {
                    $isUnBlock = UserBlock::where('blockedUserId',$isBanned->id)->where('blockedBy',Auth::id())->first();
                    if ($isUnBlock){
                        $isUnBlock->delete();
                    }
                    DB::commit();
                    return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
                }
            }else{
                return response()->json(['response' => ['status' => false, 'message' => 'User id not found!']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveExceptionLog($e, 'AuthController user Block exception');
            return response()->json(['response' => ['status' => false, 'message' => $e->getMessage()]], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

}
