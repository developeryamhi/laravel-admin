<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingGroupsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("settings-module::table_setting_groups"))) {
            Schema::create(app("config")->get("settings-module::table_setting_groups"), function(Blueprint $table) {
                $table->increments('id');

                $table->string('setting_group_key')->unique();
                $table->string('setting_group_name_lang');
                $table->text('setting_group_desc_lang');
                $table->integer('setting_full_row')->default(0);

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
        Schema::dropIfExists(app("config")->get("settings-module::table_setting_groups"));
    }

}
