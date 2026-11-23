<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Database\Seeder;
use Kreait\Firebase\Factory;

class UserFirebaseDeleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(FirebaseService $firebase)
    {
        $firebase->deleteFirebaseUser();        
    }
}
