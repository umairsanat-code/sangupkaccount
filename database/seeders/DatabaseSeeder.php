<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
//        $this->call(CategorySubcategorySeeder::class);
//        $this->call(UserFirebaseDeleteSeeder::class);
        $this->call(SuperAdminSeeder::class);
        $this->call(PermissionsSeeder::class);
//        $this->call(CommissionSetting::class);
//        $this->call(UserSeeder::class);
    }
}
