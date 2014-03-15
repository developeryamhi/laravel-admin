<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("auth-module::table_permissions"))) {
            Schema::create(app("config")->get("auth-module::table_permissions"), function(Blueprint $table) {
                $table->increments('id');

                $table->string("permission_key");
                $table->text("permission_description");

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
        Schema::dropIfExists(app("config")->get("auth-module::table_permissions"));
    }

}
