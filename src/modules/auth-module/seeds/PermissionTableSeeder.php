<?php namespace Developeryamhi\AuthModule;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTableSeeder extends Seeder {

    public function run() {
        DB::table(app("config")->get("auth-module::table_permissions"))->delete();

        //  Add Dashboard Permissions
        PermissionItem::addPermission('dashboard', 'Dashboard Access');

        //  Add Users Management Permissions
        PermissionItem::addPermission('manage_users', 'Users Management Rights');
    }
}
