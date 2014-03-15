<?php namespace Developeryamhi\AuthModule;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserMetaTableSeeder extends Seeder {

    public function run() {
        DB::table(app("config")->get("auth-module::table_user_metas"))->delete();
    }
}
