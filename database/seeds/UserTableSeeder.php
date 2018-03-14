<?php

use Illuminate\Database\Seeder;
use \App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make();

        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());

        $user = User::find(1);
        $user->name = 'Sylar.Bigb';
        $user->email = '28239337@qq.com';
        $user->is_admin = true;
        $user->password = bcrypt('qwe123123');
        $user->save();
    }
}
