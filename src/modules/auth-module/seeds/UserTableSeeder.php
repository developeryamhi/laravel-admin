<?php namespace Developeryamhi\AuthModule;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder {

    public function run() {
        $adminGroup = GroupItem::findGroup(adminGroup());
        if($adminGroup) {
            UserItem::create(array(
                'group_id' => $adminGroup->id,
                'full_name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@avanzait.com',
                'password' => Hash::make('password')
            ));
        }
    }
}
