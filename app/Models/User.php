<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    protected $dateFormat = 'U';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName', 'lastName', 'email', 'phoneNumber', 'password', 'two_factor_secret',
        'two_factor_recovery_codes', 'avatar', 'uid', 'isEmailVerified', 'isPhoneVerified',
        'paypalCustomerId', 'onHoldPayment', 'clearedPayment', 'status', 'fbId', 'googleId', 'appleId',
        'sellerReviews','sellerRating','buyerRating','buyerReviews'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $appends = ['name', 'avatarFullUrl'];

    /**
     * getNameAttribute return user full name
     *
     * @return string
     */

     /**
      * activeOrder
      *
      * @var array
      */
     protected $activeOrder=['ACCEPTED','TRACKING','RATING'];

    public function getNameAttribute()
    {
        return $this->attributes['firstName'] . ' ' . $this->attributes['lastName'];
    }
    /**
     * getAvatarFullUrlAttribute : return full url of avatar
     *
     * @return void
     */
    public function getAvatarFullUrlAttribute()
    {
        return is_null($this->attributes['avatar']) ? asset('img/default-avatar.svg')
         : Storage::disk('s3')->url($this->attributes['avatar']);
    }
    public function sellers()
    {
        return $this->hasOne(SellerDocument::class, 'userId', 'id')->orderBy('createdAt','desc');
    }
    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class, 'userId', 'id');
    }

    /**
     * document: used to get document status
     *
     * @return void
     */
    public function document()
    {
        return $this->hasOne(SellerDocument::class, 'userId', 'id');
    }


    public function documents()
    {
        return $this->hasMany(SellerDocument::class, 'userId', 'id');
    }
    public function carts(){
        return $this->hasMany(Cart::class,'buyerId','id');
    }
    public function sellerEarnings(){
        return $this->hasMany(Order::class,'sellerId','id');
    }
    public function products(){
        return $this->hasMany(Product::class,'sellerId','id');
    }

    public function sellerOrders(){
        return $this->hasMany(Order::class,'sellerId');
    }
    public function buyerOrders(){
        return $this->hasMany(Order::class,'buyerId');
    }
    public function buyerOrdersActive(){
        return $this->hasMany(Order::class,'buyerId')->whereIn('status',$this->activeOrder);
    }
    public function userBlocks(){
        return $this->hasMany(UserBlock::class,'blockedBy','id');
    }
    public function tickets() {
        return $this->hasMany(Ticket::class, 'author_name', 'name'); // author_name سے نام کا میچ
    }

}
