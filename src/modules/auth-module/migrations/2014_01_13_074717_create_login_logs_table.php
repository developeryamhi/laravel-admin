<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("auth-module::table_login_logs"))) {
            Schema::create(app("config")->get("auth-module::table_login_logs"), function(Blueprint $table) {
                $table->increments('id');

                $table->string('username');
                $table->string('ip_address');
                $table->integer("success")->default(0);

                $table->timestamp('attempt_at');
                $table->timestamp('logout_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(app("config")->get("auth-module::table_login_logs"));
    }

}
