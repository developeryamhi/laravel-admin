<?php

//  Listen Error
App::error(function($exception) {

    //  Get Path Info
    $pathInfo = trim(currentPathInfo(), "/") . "/";

    //  Get Admin Alias Path
    $adminAliasPath = adminAliasPath();

    //  Get Status Code
    $status_code = (method_exists($exception, "getStatusCode") ? $exception->getStatusCode() : 0);

    //  Match
    if($status_code != 404 && substr($pathInfo, 0, strlen($adminAliasPath)) == $adminAliasPath) {

        //  Clear Contents
        $level = ob_get_level();
        while($level > 0) {
            @ob_clean();
            $level--;
        }

        //  Return Response
        return Response::view('laravel-admin::errors.error', array(
            "errorTitle" => "Application Error",
            "exception" => $exception
        ), 500);
    }
});

//  Listen 404
App::missing(function($exception) {

    //  Get Path Info
    $pathInfo = currentPathInfo();

    //  Get Admin Alias Path
    $adminAliasPath = adminAliasPath();

    //  Match
    if(substr($pathInfo, 0, strlen($adminAliasPath)) == $adminAliasPath) {

        //  Return Response
        return Response::view('laravel-admin::errors.missing', array(
            "errorTitle" => "404 Page not Found",
            "exception" => $exception
        ), 404);
    }
});

App::down(function()
{

    //  Get Path Info
    $pathInfo = trim(currentPathInfo(), "/");

    //  Allowed
    $allowed = false;

    //  Check for Login Page
    if(currentPathUrl() == urlRoute(\Developeryamhi\AuthModule\UserItem::loginRoute(), \Developeryamhi\AuthModule\UserItem::loginRouteParams())
            || currentPathUrl() == urlRoute(\Developeryamhi\AuthModule\UserItem::logoutRoute(), \Developeryamhi\AuthModule\UserItem::logoutRouteParams()))
        $allowed = true;

    //  Check for User
    if(!$allowed && Auth::check() && Auth::user()->canAccess("maintainance_mode"))
        $allowed = true;

    //  Get IPs if Setting Created
    $maintainance_ip_use_as = getSetting("maintainance_ip_use_as");
    $maintainance_ips = getSetting("maintainance_ips");

    //  Check if Setting is Valid
    if($maintainance_ip_use_as && $maintainance_ips && !empty($maintainance_ips)) {

        //  IP List
        $ip_list = explode("\r\n", $maintainance_ips);

        //  Current Visitor IP
        $currentIP = Request::getClientIp();

        //  Found
        $found = (in_array($currentIP, $ip_list));

        //  Check Found
        if($found && $maintainance_ip_use_as == "whitelist")
            $allowed = true;
        else if(!$found && $maintainance_ip_use_as == "whitelist")
            $allowed = false;
        else if($found && $maintainance_ip_use_as == "blacklist")
            $allowed = false;
        else if(!$found && $maintainance_ip_use_as == "blacklist")
            $allowed = true;
    }

    //  Check for Scripts, Styles & Images
    if(!$allowed && (in_array(substr($pathInfo, -3), array(".js")) || in_array(substr($pathInfo, -4), array(".css", ".jpg", ".png")))) {

        //  Set Allowed
        $allowed = true;
    }

    //  Check Allowed
    if(!$allowed) {

        return Response::view("laravel-admin::maintainance", array(
            "errorTitle" => "System Undergoing Changes"
        ), 503);
    }
});