<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\DocumentImage;
use App\Models\SellerDocument;
use Illuminate\Database\Seeder;
use App\Services\FirebaseService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            $firebase= new FirebaseService();
            $faker = \Faker\Factory::create();

            $password ='TechSwivel#001';
            ///////////////////////////////////// BUYERS/////////////////////////////////////////////////
                        


            $firstName = 'Musaddiq';
            $lastName = 'Buyer';
            $email =  'musaddiq.ansar@techswivel.com';
            $phoneNumber = '+923437435366';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('buyer');



            $firstName = 'Khawar';
            $lastName = 'Buyer';
            $email =  'khawar1@gmail.com';
            $phoneNumber = '+923096065940';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('buyer');

            


            $firstName = 'Anas';
            $lastName = 'Nawaz Buyer';
            $email =  'anas.nawaz@techswivel.com';
            $phoneNumber = '+923027226074';         
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('buyer');


            $firstName = 'Assadullah';
            $lastName = 'Khan Niazi Buyer';
            $email =  'assadullahkhan.niazi@techswivel.com';
            $phoneNumber = '+923366666891';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('buyer');


            $firstName = 'Hamza';
            $lastName = 'Javed Buyer';
            $email =  'hamzajaved.bhatti@techswivel.com';
            $phoneNumber = '+923029444742';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('buyer');


            $firstName = 'Rana';
            $lastName = 'Ased Ahmad Buyer';
            $email =  'ranaased.ahmad@techswivel.com';
            $phoneNumber = '+923006998703';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('buyer');

            
            

            $firstName = 'Zain Ul Abdeen';
            $lastName = 'Buyer';
            $email =  'zainulabdeen@techswivel.com';
            $phoneNumber = '+923132522448';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('buyer');











            ///////////////////////////////////// SELLERS/////////////////////////////////////////////////

            $img = "documents.png";
            $imageStorePath=rtrim("uploads/sellerDocuments", '/').'/'.$img;
            $firstFile = Image::make(public_path("sellerDocs/".$img))->resize(300, 300);
            Storage::disk('s3')->put($imageStorePath, $firstFile->stream());

            $firstName = 'Musaddiq';
            $lastName = 'Seller';
            $email =  'musaddaq.ansar@gmail.com';
            $phoneNumber = '+923490999016';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('seller');
            $document= SellerDocument::create([
                'userId' => $user->id,
                'status' => 'APPROVED',
                'latitude' => '31.42335355',
                'longitude' => '31.42335355',
                'address' => $faker->address()
            ]);
            DocumentImage::create(
                [
                    'documentId'=>$document->id,
                    'documentUrl' => "uploads/sellerDocuments/".$img
                ]
            );




            $firstName = 'Khawar';
            $lastName = 'Seller';
            $email =  'Khawar@gmail.com';
            $phoneNumber = '+923096065949';    
            $firebaseUser = $firebase->createUserAccount(['email' => $email, 'password' => $password]);
            $user = User::create([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email ,
                'phoneNumber' => $phoneNumber,
                'password' => Hash::make($password),
                'status' => 'Active',
                'uid'=> $firebaseUser->data->uid??null
            ]);
            $user->assignRole('seller');
            $document= SellerDocument::create([
                'userId' => $user->id,
                'status' => 'APPROVED',
                'latitude' => '31.42335355',
                'longitude' => '31.42335355',
                'address' => $faker->address()
            ]);
            DocumentImage::create(
                [
                    'documentId'=>$document->id,
                    'documentUrl' => "uploads/sellerDocuments/".$img
                ]
            );

            

    }


}
