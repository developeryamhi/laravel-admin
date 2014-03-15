<?php

/**
 * Validate Controller Permission
 */
Event::listen("controller.filter.before", function($controller) {

    //  Get Permissions Required List
    $permissions = $controller->permissionRequired();

    //  Validate
    if($permissions) {

        //  Filter Data
        if(!is_array($permissions)) $permissions = explode("|", $permissions);

        //  Get Route Info
        $routeInfo = routeInfo();

        //  Check for Exclude
        if(!$controller->excludeFromPermissions($routeInfo["method"], $permissions)) {

            //  Loop Each Permission
            foreach($permissions as $permission) {

                //  Check Permission
                if(!userHasPermission($permission)) {

                    //  Trigger Access Denied Event
                    app("events")->fire("controller.permission.denied", array($this, app()));

                    //  Run Access Denied Method
                    return $controller->accessDenied();
                    break;
                }
            }
        }
    }
});

/**
 * Register New User Type
 */
function registerUserType($name, $description, $has_interface = false, $routes = null, $langs = null, $permission = null, $perm_group = null) {

    //  Search if Already Existing
    $group = \Developeryamhi\AuthModule\GroupItem::findGroup($name);

    //  Routes
    $routes || $routes = array();

    //  Langs
    $langs || $langs = array();

    //  Check if Not Exists
    if(!$group) {

        //  Create New
        $group = new \Developeryamhi\AuthModule\GroupItem();
    }

    //  Assign Properties
    $group->group_name = $name;
    $group->group_description = $description;
    $group->has_interface = (int)((bool)$has_interface);
    $group->group_permission = (string)$permission;
    $group->group_routes = serialize($routes);
    $group->group_langs = serialize($langs);

    //  Save Group
    $group->save();

    //  Add Dashboard Permission to User
    getDashboardPermission()->getAdded($group);

    //  Check Permissions to Add to Groups
    if($permission && $perm_group) {

        //  Validate Array
        if(!is_array($perm_group))    $perm_group = array($perm_group);

        //  Create Permission
        $perm = \Developeryamhi\AuthModule\PermissionItem::addPermission($permission, "Manage " . $group->group_description);

        //  Loop Each
        foreach($perm_group as $permG) {

            //  Add Manage Permission to Group
            \Developeryamhi\AuthModule\GroupItem::findGroup($permG)->addPermission($perm);
        }
    }

    return $group;
}

/**
 * Check User Has Permission
 */
function userHasPermission($permission) {
    if(!Auth::check())   return false;
    return Auth::user()->canAccess($permission);
}

/**
 * Check Admin
 */
function isAdmin() {
    if(!Auth::check())   return false;
    return Auth::user()->isGroup(adminGroup());
}

/**
 * Get Admin Group
 */
function adminGroup() {
    return app("config")->get("auth-module::admin_group");
}

/**
 * Get Permission
 */
function getPermission($name) {
    return \Developeryamhi\AuthModule\PermissionItem::findPermission($name);
}

/**
 * Get Dashboard Permission
 */
function getDashboardPermission() {
    return \Developeryamhi\AuthModule\PermissionItem::forDashboard();
}

/**
 * Create Permission
 */
function addPermission($key, $description) {
    return \Developeryamhi\AuthModule\PermissionItem::addPermission($key, $description);
}

/**
 * Find Group
 */
function findGroup($name) {
    return \Developeryamhi\AuthModule\GroupItem::findGroup($name);
}

/**
 * Add Permission to Group
 */
function addGroupPermission($group, $permission_key, $permission_description) {
    if(!is_array($group))
        $group = array($group);
    foreach($group as $grp) {
        $groupObj = findGroup($grp);
        if($groupObj)
            $groupObj->addPermission(addPermission($permission_key, $permission_description));
    }
}

/**
 * Add Permission to Admin Group Helper
 */
function addAdminPermission($permission_key, $permission_description) {
    addGroupPermission(adminGroup(), $permission_key, $permission_description);
}