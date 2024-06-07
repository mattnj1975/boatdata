<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usercheck = User::where('email', 'master@yop.com')->first();
        if ($usercheck != null) {
            $usercheck->delete();
        }
        $user = new User();
        $user->name = 'Master User';
        $user->email = 'master@yop.com';
        $user->role_as = 2;
        $user->password = Hash::make('123456');
        $user->save();

    }
}
