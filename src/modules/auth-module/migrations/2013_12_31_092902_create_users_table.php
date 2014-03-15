<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("auth-module::table_users"))) {
            Schema::create(app("config")->get("auth-module::table_users"), function(Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('group_id');
                $table->foreign('group_id')->references('id')->on(app("config")->get("auth-module::table_groups"));

                $table->text('full_name');
                $table->string('nicename', 255);
                $table->string('email', 255)->unique();
                $table->string('username', 32)->unique();
                $table->string('password', 100);
                $table->integer("enabled")->default(1);

                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(app("config")->get("auth-module::table_users"));
    }
}
