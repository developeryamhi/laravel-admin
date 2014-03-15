<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMetasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("auth-module::table_user_metas"))) {
            Schema::create(app("config")->get("auth-module::table_user_metas"), function(Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on(app("config")->get("auth-module::table_users"));

                $table->string("meta_key");
                $table->text("meta_value");

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(app("config")->get("auth-module::table_user_metas"));
    }

}
