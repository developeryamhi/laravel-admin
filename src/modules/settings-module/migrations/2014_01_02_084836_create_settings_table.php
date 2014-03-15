<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasTable(app("config")->get("settings-module::table_settings"))) {
            Schema::create(app("config")->get("settings-module::table_settings"), function(Blueprint $table) {
                $table->increments('id');

                $table->unsignedInteger('setting_group_id');
                $table->foreign('setting_group_id')->references('id')->on(app("config")->get("settings-module::table_setting_groups"));

                $table->string('setting_key')->unique();
                $table->text('setting_value');
                $table->string('setting_name_lang');
                $table->string('setting_desc_lang');
                $table->string('setting_type')->default('text');
                $table->integer('setting_required')->default(1);
                $table->string('setting_options');
                $table->integer('setting_mchoice')->default(0);
                $table->integer('has_interface')->default(1);
                $table->integer("autoload")->default(0);

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
        Schema::dropIfExists(app("config")->get("settings-module::table_settings"));
    }

}
