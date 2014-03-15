<?php namespace Developeryamhi\AuthModule;

use Illuminate\Database\Seeder;

class GroupToPermissionTableSeeder extends Seeder {

    public function run() {

        //  Add Dashboard Permissions
        GroupItem::findGroupAndAddPermission(adminGroup(), "dashboard");

        //  Add Users Management Permissions
        GroupItem::findGroupAndAddPermission(adminGroup(), "manage_users");
    }
}
