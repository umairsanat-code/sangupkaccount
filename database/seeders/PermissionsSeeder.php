<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        $permssions = array(

            [
                'name' => 'view-user',
                'moduleName' => 'clients',
            ],
            [
                'name' => 'create-user',
                'moduleName' => 'clients',
            ],
            [
                'name' => 'edit-user',
                'moduleName' => 'clients',
            ],
            [
                'name' => 'view-user',
                'moduleName' => 'teans',
            ],
            [
                'name' => 'create-user',
                'moduleName' => 'teams',
            ],
            [
                'name' => 'edit-user',
                'moduleName' => 'teams',
            ],

            [
                'moduleName' => 'manager',
                'name' => 'view-teams',
            ] ,
            [
                'moduleName' => 'manager',
                'name' => 'view-teams',
            ]



        );




        foreach ($permssions as $p) {

            $isNull = Permission::where('name', $p['name'])->first();
            if (is_null($isNull)) {
                Permission::create([
                    'name' => $p['name'],
                    'moduleName' => $p['moduleName']
                ]);
            }
        }
    }
}
