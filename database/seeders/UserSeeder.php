<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'info@xtreem.com')->first();
        if (is_null($user)) {
            $user = new User();
            $user->name = "Xtreem Erp";
            $user->phone = "01854125454";
            $user->email = "info@xtreem.com";
            $user->password = Hash::make('12345678');
            $user->role_id = 1;
            $user->save();
        }

        $user = User::where('email', 'info@demo.com')->first();
        if (is_null($user)) {
            $user = new User();
            $user->name = "Demo Company";
            $user->phone = "01800000000";
            $user->email = "info@demo.com";
            $user->password = Hash::make('demo');
            $user->role_id = 1;
            $user->save();
        }
    }
}
