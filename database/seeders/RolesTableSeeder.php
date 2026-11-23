<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        if (Role::where('name', '=', 'admin')->first() === null) {
            Role::create(['name' => 'admin']);
        }
        if (Role::where('name', '=', 'seller')->first() === null) {
            Role::create(['name' => 'seller']);

        }
        if (Role::where('name', '=', 'buyer')->first() === null) {
            Role::create(['name' => 'buyer']);
        }


    }
}
