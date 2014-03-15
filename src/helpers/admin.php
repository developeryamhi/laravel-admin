<?php

/**
 * Dump and Exit
 */
function dump_exit($var) {
    var_dump($var);
    exit;
}

/**
 * Echo and Exit
 */
function echo_exit($var) {
    echo (string)$var;
    exit;
}

/**
 * Generate Base URL
 */
function createBaseUrl($path = '') {
    $http_type = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "On" ? "https" : "http");
    $http_root = $_SERVER['HTTP_HOST'];
    $http_folder = dirname($_SERVER['PHP_SELF']) . '/';
    $base_url = $http_type . "://" . $http_root . $http_folder;
    return trim($base_url . $path, "/");
}

/**
 * Get Current Page URL
 */
function currentPathUrl() {
    return createBaseUrl(currentPathInfo());
}

/**
 * Get Current Path Info
 */
function currentPathInfo() {
    $base_url = createBaseUrl();
    $explodes = explode("/", $base_url);
    unset($explodes[0]);unset($explodes[1]);unset($explodes[2]);
    $explodes = array_values($explodes);

    $path_info = trim($_SERVER["REQUEST_URI"], "/");
    foreach($explodes as $explode) {
        if(!empty($explode)) {
            if($explode . "/" == substr($path_info, 0, strlen($explode) + 1)) {
                $path_info = substr($path_info, strlen($explode) + 1);
            }
        }
    }
    return $path_info;
}

/**
 * Get URL By Route
 */
function urlRoute($route, $params = array()) {
    return app("url")->route($route, $params);
}

/**
 * Get Admin Layout
 */
function adminLayout() {
    return mergeEventFireResponse("laravel-admin::layouts.admin", app("events")->fire("admin.layout.main", array(app(), "laravel-admin::layouts.admin")));
}

/**
 * Get Guest Layout
 */
function guestLayout() {
    return mergeEventFireResponse("laravel-admin::layouts.guest", app("events")->fire("admin.layout.guest", array(app(), "laravel-admin::layouts.guest")));
}

/**
 * Get Admin Alias
 */
function adminAlias() {
    return app("laravel-admin")->alias();
}

/**
 * Get Admin Alias Path
 */
function adminAliasPath() {
    return app("laravel-admin")->aliasPath();
}

/**
 * Get Admin Nav Group
 */
function adminNavGroup() {
    return app("laravel-admin")->navGroup();
}

/**
* Get Admin Theme Route Name
*/
function adminThemeUrlRoute() {
   return app("laravel-admin")->adminThemeUrlRoute();
}

/**
* Get Admin Style Route Name
*/
function adminStyleUrlRoute() {
   return app("laravel-admin")->adminStyleUrlRoute();
}

/**
* Get Admin Script Route Name
*/
function adminScriptUrlRoute() {
   return app("laravel-admin")->adminScriptUrlRoute();
}

/**
* Get Guest Theme Route Name
*/
function guestThemeUrlRoute() {
   return app("laravel-admin")->guestThemeUrlRoute();
}

/**
* Get Guest Style Route Name
*/
function guestStyleUrlRoute() {
   return app("laravel-admin")->guestStyleUrlRoute();
}

/**
* Get Guest Script Route Name
*/
function guestScriptUrlRoute() {
   return app("laravel-admin")->guestScriptUrlRoute();
}

/**
* Get Error Theme Route Name
*/
function errorThemeUrlRoute() {
   return app("laravel-admin")->errorThemeUrlRoute();
}

/**
* Get Guest Style Route Name
*/
function errorStyleUrlRoute() {
   return app("laravel-admin")->errorStyleUrlRoute();
}

/**
* Get Guest Script Route Name
*/
function errorScriptUrlRoute() {
   return app("laravel-admin")->errorScriptUrlRoute();
}

/**
 * Set Admin Theme to Use
 */
function setAdminThemeToUse($name, $clear = false) {
    return app("laravel-admin")->setThemeToUse("admin", $name, $clear);
}

/**
 * Set Admin Style to Use
 */
function setAdminStyleToUse($name, $clear = false) {
    return app("laravel-admin")->setStyleToUse("admin", $name, $clear);
}

/**
 * Set Guest Theme to Use
 */
function setGuestThemeToUse($name, $clear = false) {
    return app("laravel-admin")->setThemeToUse("guest", $name, $clear);
}

/**
 * Set Guest Style to Use
 */
function setGuestStyleToUse($name, $clear = false) {
    return app("laravel-admin")->setStyleToUse("guest", $name, $clear);
}

/**
 * Set Error Theme to Use
 */
function setErrorThemeToUse($name, $clear = false) {
    return app("laravel-admin")->setThemeToUse("error", $name, $clear);
}

/**
 * Set Error Style to Use
 */
function setErrorStyleToUse($name, $clear = false) {
    return app("laravel-admin")->setStyleToUse("error", $name, $clear);
}

/**
 * Get Admin Themes to Use
 */
function getAdminThemesToUse() {
    return app("laravel-admin")->getThemesToUse("admin");
}

/**
 * Get Admin Styles to Use
 */
function getAdminStylesToUse() {
    return app("laravel-admin")->getStylesToUse("admin");
}

/**
 * Get Admin Styles to Use
 */
function getAdminScriptsToUse() {
    return app("laravel-admin")->getScriptsToUse("admin");
}

/**
 * Get Guest Themes to Use
 */
function getGuestThemesToUse() {
    return app("laravel-admin")->getThemesToUse("guest");
}

/**
 * Get Guest Styles to Use
 */
function getGuestStylesToUse() {
    return app("laravel-admin")->getStylesToUse("guest");
}

/**
 * Get Guest Styles to Use
 */
function getGuestScriptsToUse() {
    return app("laravel-admin")->getScriptsToUse("guest");
}

/**
 * Get Error Themes to Use
 */
function getErrorThemesToUse() {
    return app("laravel-admin")->getThemesToUse("error");
}

/**
 * Get Error Styles to Use
 */
function getErrorStylesToUse() {
    return app("laravel-admin")->getStylesToUse("error");
}

/**
 * Get Error Styles to Use
 */
function getErrorScriptsToUse() {
    return app("laravel-admin")->getScriptsToUse("error");
}

/**
 * Register Admin Theme
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerAdminTheme($name, $path) {
    return app("laravel-admin")->registerTheme("admin", $name, $path);
}

/**
 * Unregister Admin Theme
 * 
 * @param type $name
 * @return type
 */
function unregisterAdminTheme($name) {
    return app("laravel-admin")->unregisterTheme("admin", $name);
}

/**
 * Register Admin Style
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerAdminStyle($name, $path) {
    return app("laravel-admin")->registerStyle("admin", $name, $path);
}

/**
 * Unregister Admin Style
 * 
 * @param type $name
 * @return type
 */
function unregisterAdminStyle($name) {
    return app("laravel-admin")->unregisterStyle("admin", $name);
}

/**
 * Register Admin Script
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerAdminScript($name, $path) {
    return app("laravel-admin")->registerScript("admin", $name, $path);
}

/**
 * Unregister Admin Script
 * 
 * @param type $name
 * @return type
 */
function unregisterAdminScript($name) {
    return app("laravel-admin")->unregisterScript("admin", $name);
}

/**
 * Register Guest Theme
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerGuestTheme($name, $path) {
    return app("laravel-admin")->registerTheme("guest", $name, $path);
}

/**
 * Unregister Guest Theme
 * 
 * @param type $name
 * @return type
 */
function unregisterGuestTheme($name) {
    return app("laravel-admin")->unregisterTheme("guest", $name);
}

/**
 * Register Guest Style
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerGuestStyle($name, $path) {
    return app("laravel-admin")->registerStyle("guest", $name, $path);
}

/**
 * Unregister Guest Style
 * 
 * @param type $name
 * @return type
 */
function unregisterGuestStyle($name) {
    return app("laravel-admin")->unregisterStyle("guest", $name);
}

/**
 * Register Guest Script
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerGuestScript($name, $path) {
    return app("laravel-admin")->registerScript("guest", $name, $path);
}

/**
 * Unregister Guest Script
 * 
 * @param type $name
 * @return type
 */
function unregisterGuestScript($name) {
    return app("laravel-admin")->unregisterScript("guest", $name);
}

/**
 * Register Error Theme
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerErrorTheme($name, $path) {
    return app("laravel-admin")->registerTheme("error", $name, $path);
}

/**
 * Unregister Error Theme
 * 
 * @param type $name
 * @return type
 */
function unregisterErrorTheme($name) {
    return app("laravel-admin")->unregisterTheme("error", $name);
}

/**
 * Register Error Style
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerErrorStyle($name, $path) {
    return app("laravel-admin")->registerStyle("error", $name, $path);
}

/**
 * Unregister Error Style
 * 
 * @param type $name
 * @return type
 */
function unregisterErrorStyle($name) {
    return app("laravel-admin")->unregisterStyle("error", $name);
}

/**
 * Register Error Script
 * 
 * @param type $name
 * @param type $path
 * @return type
 */
function registerErrorScript($name, $path) {
    return app("laravel-admin")->registerScript("error", $name, $path);
}

/**
 * Unregister Error Script
 * 
 * @param type $name
 * @return type
 */
function unregisterErrorScript($name) {
    return app("laravel-admin")->unregisterScript("error", $name);
}

/**
 * Register Admin Route
 */
function register_admin_route($uri, $action = array(), $type = "get") {
    if($type == "get")
        return Route::get(adminAliasPath() . $uri, $action);
    else if($type == "post")
        return Route::post(adminAliasPath() . $uri, $action);
    else if($type == "patch")
        return Route::patch(adminAliasPath() . $uri, $action);
    else if($type == "put")
        return Route::put(adminAliasPath() . $uri, $action);
    else if($type == "options")
        return Route::options(adminAliasPath() . $uri, $action);
    else if($type == "delete")
        return Route::delete(adminAliasPath() . $uri, $action);
    else
        return Route::any(adminAliasPath() . $uri, $action);
}

/**
 * Register POST Admin Route
 */
function register_admin_route_post($uri, $action = array()) {
    return register_admin_route($uri, $action, "post");
}

/**
 * Register PATCH Admin Route
 */
function register_admin_route_patch($uri, $action = array()) {
    return register_admin_route($uri, $action, "patch");
}

/**
 * Register PUT Admin Route
 */
function register_admin_route_put($uri, $action = array()) {
    return register_admin_route($uri, $action, "put");
}

/**
 * Register OPTIONS Admin Route
 */
function register_admin_route_options($uri, $action = array()) {
    return register_admin_route($uri, $action, "options");
}

/**
 * Register DELETE Admin Route
 */
function register_admin_route_delete($uri, $action = array()) {
    return register_admin_route($uri, $action, "delete");
}

/**
 * Register any Admin Route
 */
function register_admin_route_any($uri, $action = array()) {
    return register_admin_route($uri, $action, "any");
}

/**
 * Get Admin Asset Path
 */
function adminAssetPath($file = null, $type = "path", $url = true) {

    //  Get Assets Folder
    $assets_folder = app("laravel-admin")->getConfig("assets_folder");
    if($assets_folder)  $assets_folder .= "/";

    //  Path
    $path = null;

    //  Find Paths
    switch(strtolower($type)) {
        case 'css':
            $path = ($url ? cssUrl($assets_folder . $file) : cssPath($assets_folder . $file));
            break;
        case 'js':
            $path = ($url ? jsUrl($assets_folder . $file) : jsPath($assets_folder . $file));
            break;
        case 'image':
            $path = ($url ? imageUrl($assets_folder . $file) : imagePath($assets_folder . $file));
            break;
        case 'path':
            $path = ($url ? assetsPath($assets_folder) : assetsPath($assets_folder));
            break;
    }

    //  Return
    return $path;
}

/**
 * Get Admin CSS Path
 */
function adminCssAssetPath($file) {
    return adminAssetPath($file, "css", false);
}

/**
 * Get Admin JS Path
 */
function adminJsAssetPath($file) {
    return adminAssetPath($file, "js", false);
}

/**
 * Get Admin Image Path
 */
function adminImageAssetPath($file) {
    return adminAssetPath($file, "image", false);
}

/**
 * Get Admin CSS URL
 */
function adminCssAssetURL($file) {
    return adminAssetPath($file, "css");
}

/**
 * Get Admin JS URL
 */
function adminJsAssetURL($file) {
    return adminAssetPath($file, "js");
}

/**
 * Get Admin Image URL
 */
function adminImageAssetURL($file) {
    return adminAssetPath($file, "image");
}

/**
 * Display Alert Message
 */
function displayAlertMsg($msg, $type, $closable = false) {
    return '<div class="alert alert-' . $type . ($closable ? ' alert-dismissable' : '') . '">' . 
        ($closable ? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' : '') . 
        $msg . 
    '</div>';
} 

/**
 * Display Info Alert
 */
function infoAlertMsg($msg, $closable = false) {
    return displayAlertMsg($msg, 'info', $closable);
}

/**
 * Display Success Alert
 */
function successAlertMsg($msg, $closable = false) {
    return displayAlertMsg($msg, 'success', $closable);
}

/**
 * Display Warning Alert
 */
function warningAlertMsg($msg, $closable = false) {
    return displayAlertMsg($msg, 'warning', $closable);
}

/**
 * Display Error Alert
 */
function errorAlertMsg($msg, $closable = false) {
    return displayAlertMsg($msg, 'danger', $closable);
}