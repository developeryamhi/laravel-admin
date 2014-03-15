<?php

//  Auth Routes
Route::get('login', array('as' => 'login', 'uses' => '\\Developeryamhi\AuthModule\\AuthController@showLogin'))->before('guest');
Route::post('login', array('uses' => '\\Developeryamhi\AuthModule\\AuthController@doLogin'))->before('guest');
Route::get('logout', array('as' => 'logout', 'uses' => '\\Developeryamhi\AuthModule\\AuthController@doLogout'))->before('auth');

//  My Account Route
register_admin_route(adminAliasPath() . 'my_account', array('as' => 'my_account', 'uses' => '\\Developeryamhi\AuthModule\\UserController@my_account'))->before('auth');
register_admin_route_post(adminAliasPath() . 'my_account', array('as' => 'update_my_account', 'uses' => '\\Developeryamhi\AuthModule\\UserController@update_my_account'))->before('auth');

//  Users Management Routes
register_admin_route(adminAliasPath() . 'users', array('as' => 'users', 'uses' => '\\Developeryamhi\AuthModule\\UserController@index'))->before('auth');
register_admin_route(adminAliasPath() . 'create_user', array('as' => 'create_user', 'uses' => '\\Developeryamhi\AuthModule\\UserController@create'))->before('auth');
register_admin_route(adminAliasPath() . 'edit_user/{id}', array('as' => 'edit_user', 'uses' => '\\Developeryamhi\AuthModule\\UserController@edit'))->before('auth');
register_admin_route_post(adminAliasPath() . 'save_user', array('as' => 'save_user', 'uses' => '\\Developeryamhi\AuthModule\\UserController@save'));
register_admin_route_post(adminAliasPath() . 'save_user/{id}', array('as' => 'update_user', 'uses' => '\\Developeryamhi\AuthModule\\UserController@save'));
register_admin_route(adminAliasPath() . 'delete_user/{id}', array('as' => 'delete_user', 'uses' => '\\Developeryamhi\AuthModule\\UserController@delete'))->before('auth');

//  ACL Groups Routes
register_admin_route(adminAliasPath() . 'groups', array('as' => 'groups', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@groups'))->before('auth');
register_admin_route(adminAliasPath() . 'create_group', array('as' => 'create_group', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@group_create'))->before('auth');
register_admin_route(adminAliasPath() . 'edit_group/{id}', array('as' => 'edit_group', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@group_edit'))->before('auth');
register_admin_route_post(adminAliasPath() . 'save_group', array('as' => 'save_group', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@group_save'));
register_admin_route_post(adminAliasPath() . 'save_group/{id}', array('as' => 'update_group', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@group_save'));
register_admin_route(adminAliasPath() . 'delete_group/{id}', array('as' => 'delete_group', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@group_delete'))->before('auth');

//  ACL Permissions Routes
register_admin_route(adminAliasPath() . 'permissions', array('as' => 'permissions', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@permissions'))->before('auth');
register_admin_route(adminAliasPath() . 'create_permission', array('as' => 'create_permission', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@permission_create'))->before('auth');
register_admin_route(adminAliasPath() . 'edit_permission/{id}', array('as' => 'edit_permission', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@permission_edit'))->before('auth');
register_admin_route_post(adminAliasPath() . 'save_permission', array('as' => 'save_permission', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@permission_save'));
register_admin_route_post(adminAliasPath() . 'save_permission/{id}', array('as' => 'update_permission', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@permission_save'));
register_admin_route(adminAliasPath() . 'delete_permission/{id}', array('as' => 'delete_permission', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@permission_delete'))->before('auth');

//  AJAX Routes
register_admin_route_post(adminAliasPath() . 'user_exists', array('as' => 'user_exists', 'uses' => '\\Developeryamhi\AuthModule\\UserController@userExists'))->before('auth');
register_admin_route_post(adminAliasPath() . 'group_exists', array('as' => 'group_exists', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@groupExists'))->before('auth');
register_admin_route_post(adminAliasPath() . 'permission_exists', array('as' => 'permission_exists', 'uses' => '\\Developeryamhi\AuthModule\\ACLController@permissionExists'))->before('auth');
register_admin_route_post(adminAliasPath() . 'users_autocomplete', array('as' => 'users_autocomplete', 'uses' => '\\Developeryamhi\AuthModule\\UserController@userLists'))->before('auth');

//  Check Module Migrated
if($this->migrationsDone()) {

    //  Get Groups with Interfaces
    $groups = \Developeryamhi\AuthModule\GroupItem::hasInterface()->get();

    //  Loop Each Groups
    $groups->each(function($group) {

        //  Check Routes Save
        if($group->hasValidInterface()) {

            //  Register Group Routes
            register_admin_route(adminAliasPath() . $group->getRoute("list") . "/{group?}", array('as' => $group->getRoute("list"), 'uses' => '\\Developeryamhi\AuthModule\\UserController@custom_index'))->before('auth');
            register_admin_route(adminAliasPath() . $group->getRoute("create"), array('as' => $group->getRoute("create"), 'uses' => '\\Developeryamhi\AuthModule\\UserController@custom_create'))->before('auth');
            register_admin_route(adminAliasPath() . $group->getRoute("edit") . '/{id}', array('as' => $group->getRoute("edit"), 'uses' => '\\Developeryamhi\AuthModule\\UserController@custom_edit'))->before('auth');
            register_admin_route_post(adminAliasPath() . $group->getRoute("save"), array('as' => $group->getRoute("save"), 'uses' => '\\Developeryamhi\AuthModule\\UserController@custom_save'));
            register_admin_route_post(adminAliasPath() . $group->getRoute("save") . '/{id}', array('as' => $group->getRoute("update"), 'uses' => '\\Developeryamhi\AuthModule\\UserController@custom_update'));
            register_admin_route(adminAliasPath() . $group->getRoute("delete") . '/{id}', array('as' => $group->getRoute("delete"), 'uses' => '\\Developeryamhi\AuthModule\\UserController@custom_delete'))->before('auth');
        }
    });
}