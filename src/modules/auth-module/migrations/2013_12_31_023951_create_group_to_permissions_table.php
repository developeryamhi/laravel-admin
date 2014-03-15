<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupToPermissionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("auth-module::table_group_to_permissions"))) {
            Schema::create(app("config")->get("auth-module::table_group_to_permissions"), function(Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('group_id');
                $table->foreign('group_id')->references('id')->on(app("config")->get("auth-module::table_groups"));

                $table->unsignedInteger('permission_id');
                $table->foreign('permission_id')->references('id')->on(app("config")->get("auth-module::table_permissions"));

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
        Schema::dropIfExists(app("config")->get("auth-module::table_group_to_permissions"));
    }

}
