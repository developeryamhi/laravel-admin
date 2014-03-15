<?php

//  Apply Admin Filter
Route::filter('admin', function() {
    if (Auth::guest())
        return Redirect::route(\Developeryamhi\AuthModule\UserItem::loginRoute(), \Developeryamhi\AuthModule\UserItem::loginRouteParams())
                        ->with('msg_error', trans("auth-module::message.must_login"));
});

//  Apply Auth Filter
Route::filter('auth', function() {
    if (Auth::guest())
        return Redirect::route(\Developeryamhi\AuthModule\UserItem::loginRoute(), \Developeryamhi\AuthModule\UserItem::loginRouteParams())
                        ->with('msg_error', trans("auth-module::message.must_login"));
});

//  Apply Guest Filter
Route::filter('guest', function() {
    if (Auth::check())
        return Redirect::route(ROUTE_DASHBOARD)
                        ->with('msg_warning', trans("auth-module::message.already_logged_in"));
});

//  Apply Permission Filter
Route::filter('permission', function($route, $request, $value) {
    $explodes = explode("~", $value);
    $permissions = explode(";", $explodes[0]);
    $valid = Auth::check();
    if($valid) {
        foreach($permissions as $permission) {
            if(!Auth::user()->canAccess($permission)) {
                $valid = false;
                break;
            }
        }
    }
    if(Auth::check() && sizeof($explodes) > 0) {
        $groups = explode(";", ltrim(rtrim($explodes[1], '}'), '{'));
        foreach($groups as $group) {
            $inv = (substr($group, 0, 1) == "!");
            if($inv)    $group = substr($group, 0, -1);
            if(!$valid && !$inv && Auth::user()->isGroup($group)) {
                $valid = true;
            }
            else if($valid && $inv && Auth::user()->isGroup($group)) {
                $valid = false;
            }
        }
    }
    if(!$valid)
        return Redirect::route(ROUTE_DASHBOARD)
                        ->with('msg_warning', trans("auth-module::message.access_denied"));
});