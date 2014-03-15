<?php namespace Developeryamhi\AuthModule;

class AuthModule {

    //  Module Migration Version
    public $migrate_version = 1.0;
    public $seed_version = 1.0;

    public function __whenReady() {

        //  Check Migrations Done
        if($this->module->migrationsDone()) {

            //  Add Permission
            addAdminPermission("manage_users", "Manage Users");

            //  Add Permission
            addAdminPermission("maintainance_mode", "Maintainance Mode Access");

            //  Add Permission
            addAdminPermission("manage_acl", "Manage Groups and Permissions");

            //  Add the Required Styles and Scripts
            registerAdminStyle("auth-admin", __DIR__ . "/styles/auth.css");
            registerAdminScript("auth-admin", __DIR__ . "/scripts/auth.js");
            setAdminStyleToUse("auth-admin");

            //  Listen Controller Ready Event
            $this->app["events"]->listen("admin.controller.ready", function() {

                //  Check Nav
                if($nav = nav(adminNavGroup())) {

                    //  Add Logout Link
                    $nav->addMenuItem("logout", trans("auth-module::menu_item.logout"), urlRoute(UserItem::logoutRoute(), UserItem::logoutRouteParams()), null, null, 1000);

                    //  Add My Account Link
                    $nav->addSubMenuItem(ADMIN_NAV_DASHBOARD, "my_account", trans("auth-module::menu_item.my_account"), urlRoute("my_account"), null, null, 1);

                    //  Check Permission
                    if(userHasPermission("manage_acl")) {

                        //  Add ACL Navigation Items
                        $nav->detectAddSubMenuItem(ADMIN_NAV_ADMINISTRATION, "acl", trans("auth-module::menu_item.acl"), urlRoute("groups"));
                        $nav->detectAddSubSubMenuItem(ADMIN_NAV_ADMINISTRATION, "acl", "groups", trans("auth-module::menu_item.groups"), urlRoute("groups"), array(
                            "include" => array("/create_group/i", "/edit_group\/(.*)/i")
                        ));
                        $nav->detectAddSubSubMenuItem(ADMIN_NAV_ADMINISTRATION, "acl", "permissions", trans("auth-module::menu_item.permissions"), urlRoute("permissions"), array(
                            "include" => array("/create_permission/i", "/edit_permission\/(.*)/i")
                        ));
                    }

                    //  Check Permission
                    if(userHasPermission("manage_users")) {

                        //  Add User Navigation Items
                        $nav->detectAddSubMenuItem(ADMIN_NAV_ADMINISTRATION, "users", trans("auth-module::menu_item.users"), urlRoute("users"));
                        $nav->detectAddSubSubMenuItem(ADMIN_NAV_ADMINISTRATION, "users", "create_user", trans("auth-module::menu_item.add_new_user"), urlRoute("create_user"));
                        $nav->detectAddSubSubMenuItem(ADMIN_NAV_ADMINISTRATION, "users", "users", trans("auth-module::menu_item.view_users"), urlRoute("users"), array(
                            "include" => array("/edit_user\/(.*)/i")
                        ));
                    }

                    //  Get Groups with Interfaces
                    $groups = GroupItem::hasInterface()->get();

                    //  Loop Each Groups
                    foreach($groups as $group) {

                        //  Check Routes & Langs
                        if($group->hasValidInterface() && (!$group->group_permission || userHasPermission($group->group_permission))) {

                            //  Add Navigation Items
                            $nav->detectAddSubMenuItem(ADMIN_NAV_ADMINISTRATION, $group->getRoute("list"), $group->getLang("menu_list"), urlRoute($group->getRoute("list")));
                            $nav->detectAddSubSubMenuItem(ADMIN_NAV_ADMINISTRATION, $group->getRoute("list"), $group->getRoute("create"), $group->getLang("menu_add_new"), urlRoute($group->getRoute("create")));
                            $nav->detectAddSubSubMenuItem(ADMIN_NAV_ADMINISTRATION, $group->getRoute("list"), $group->getRoute("list"), $group->getLang("menu_view_list"), urlRoute($group->getRoute("list")), array(
                                "include" => array("/" . $group->getRoute("edit") . "\/(.*)/i")
                            ));
                        }
                    }
                }
            });
        }
    }

    public function __whenActivated() {

        //  Do Module Migrations
        $this->module->doMigrations($this->migrate_version);

        //  Run Seeders
        $this->module->doSeeding($this->seed_version);
    }

    public function __whenUpdated() {

        //  Check for Register Ready
        if($this->module->isRegisterReady()) {

            //  Do Module Migrations
            $this->module->doMigrations($this->migrate_version);

            //  Run Seeders
            $this->module->doSeeding($this->seed_version);
        }
    }
}