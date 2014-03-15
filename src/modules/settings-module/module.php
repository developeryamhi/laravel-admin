<?php namespace Developeryamhi\SettingsModule;

class SettingsModule {

    //  Module Migration Version
    public $migrate_version = 1.0;

    public function __whenReady() {

        //  Add Permission
        addAdminPermission("manage_settings", "Manage Settings Module");

        //  Store Settings to App
        $this->app["settings_data"] = SettingItem::getSettingsConfig();

        //  Listen Controller Ready Event
        $this->app["events"]->listen("admin.controller.ready", function() {

            //  Check Nav
            if($nav = nav(adminNavGroup())) {

                //  Check Permission
                if(userHasPermission("manage_settings")) {

                    //  Add Navigation Items
                    $nav->detectAddSubMenuItem(ADMIN_NAV_SETTINGS, "settings", trans("settings-module::menu_item.settings"), urlRoute("settings"));
                }
            }
        });
    }

    public function __whenActivated() {

        //  Do Module Migrations
        $this->module->doMigrations($this->migrate_version);
    }

    public function __whenUpdated() {

        //  Check for Register Ready
        if($this->module->isRegisterReady()) {

            //  Do Module Migrations
            $this->module->doMigrations($this->migrate_version);
        }
    }
}