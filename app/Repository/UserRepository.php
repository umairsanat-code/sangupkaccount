<?php


namespace App\Repository;

use App\Enums\UserStatus;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\UserStatusEnum;
use Illuminate\Support\Collection;


class UserRepository
{

    public function add(array $user): User
    {
        return User::create($user);
    }
   public function getByFbSocialId(string $id): ?User
    {
        return User::where('fbId', $id)->first();
    }

    public function getByGmailSocialId(string $id): ?User
    {
        return User::where('googleId', $id)->first();
    }
    public function getByAppleSocialId(string $id): ?User
    {
        return User::where('appleId', $id)->first();
    }

    public function getByPhoneNumber(string $phoneNumber): ?User
    {
        return User::where('phoneNumber', $phoneNumber)->first();
    }

    public function getByPhoneEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }




}
