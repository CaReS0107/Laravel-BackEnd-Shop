<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $adminRole = Role::where('name','admin')->first();
        $userRole = Role::where('name','user')->first();
        $admin = User::create ([
           'first_name'=>'Admin' ,
           'last_name'=>'Admin' ,
            'email'=>'admin@admin.com',
            'password'=>Hash::make('admin'),
            'api_token' => Str::random(64)
        ]);
        $user = User::create ([
           'first_name'=>'user' ,
           'last_name'=>'user' ,
            'email'=>'user@user.com',
            'password'=>Hash::make('user'),
            'api_token' => Str::random(64)
        ]);
        $admin->roles()->attach($adminRole);
        $user->roles()->attach($userRole);


    }
}
