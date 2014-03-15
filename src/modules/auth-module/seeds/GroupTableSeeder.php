<?php namespace Developeryamhi\AuthModule;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupTableSeeder extends Seeder {

    public function run() {
        DB::table(app("config")->get("auth-module::table_users"))->delete();
        DB::table(app("config")->get("auth-module::table_group_to_permissions"))->delete();
        DB::table(app("config")->get("auth-module::table_groups"))->delete();

        //  Create Admin Group
        GroupItem::create(array(
            'group_name' => adminGroup(),
            'group_description' => 'Administrator'
        ));
    }
}
