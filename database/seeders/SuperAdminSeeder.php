<?php

namespace Database\Seeders;

use App\Helpers\FcmHelper as HelpersFcmHelper;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Facades\App\Helpers\FcmHelper;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         $user=User::create([
            'firstName' => 'TechSwivel',
            'lastName' => 'QA',
            'email' => 'qa@techswivel.com',
            'phoneNumber'=>'+923415353533',
            'password' => Hash::make('TechSwivel#001'),
            'status'=>'Active',

        ]);
        $user->assignRole('admin');
    }
}
