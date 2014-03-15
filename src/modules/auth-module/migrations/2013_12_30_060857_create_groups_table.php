<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("auth-module::table_groups"))) {
            Schema::create(app("config")->get("auth-module::table_groups"), function(Blueprint $table) {
                $table->increments('id');

                $table->string("group_name");
                $table->text("group_description");
                $table->integer("has_interface")->default(0);
                $table->integer("is_hidden")->default(0);
                $table->string("group_permission");
                $table->text("group_routes");
                $table->text("group_langs");

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
        Schema::dropIfExists(app("config")->get("auth-module::table_groups"));
    }

}
