<?php

//  Generate Navigation
function generate_navigation($name, $app = null) {
    $app || $app = app();
    return new \Developeryamhi\LaravelAdmin\NavigationHelper($app, $name);
}

//  Get Nav Instance
function nav($name) {
    $instance_name = \Developeryamhi\LaravelAdmin\NavigationHelper::getInstanceName($name);
    if($instance_name && app()->isShared($instance_name))
        return app($instance_name);
    return null;
}

//  Reset Resources
function resetResources($clear_registered = false) {

    //  Run Function
    return app("assets_manager")->resetResources($clear_registered);
}

//  Make View
function makeView($view = 'index', $namespace = null, $data = array(), $mergeData = array()) {

    //  Run Function
    return app("assets_manager")->makeView($view, $namespace, $data, $mergeData);
}

//  Set Page Base Title
function setBasePageTitle($title) {

    //  Run Function
    app("assets_manager")->setBasePageTitle($title);
}

//  Set Page Title
function setPageTitle($title, $hard = false) {

    //  Run Function
    app("assets_manager")->setPageTitle($title, $hard);
}

//  Set Page Seperator
function setPageTitleSeperator($sep) {

    //  Run Function
    app("assets_manager")->setPageTitleSeperator($sep);
}

//  Set Page Data
function setPageData($key, $value) {

    //  Run Function
    app("assets_manager")->setPageData($key, $value);
}

//  Check Page Has Data
function pageHasData($key) {

    //  Run Function
    return app("assets_manager")->pageHasData($key);
}

//  Get Page Data
function getPageData($key, $def = null) {

    //  Run Function
    return app("assets_manager")->getPageData($key, $def);
}

//  Remove Page Data
function removePageData($key) {

    //  Run Function
    app("assets_manager")->removePageData($key);
}

//  Register Style
function register_style($src, $name = null) {

    //  Run Function
    app("assets_manager")->enqueue_style($src, $name);
}

//  Unregister Style
function unregister_style($src, $name = null) {

    //  Run Function
    app("assets_manager")->unregister_style($src, $name);
}

//  Register Script
function register_script($src, $name = null) {

    //  Run Function
    app("assets_manager")->register_script($src, $name);
}

//  Unregister Script
function unregister_script($src, $name = null) {

    //  Run Function
    app("assets_manager")->unregister_script($src, $name);
}

//  Enqueue Style
function enqueue_style($name, $is_inline = false, $data = null, $section = VIEW_LOCATION_HEADER, $media = "all", $rel = "stylesheet", $attrs = array()) {

    //  Run Function
    app("assets_manager")->enqueue_style($name, $is_inline, $data, $section, $media, $rel, $attrs);
}

//  Unenqueue Style
function unenqueue_style($name, $section = VIEW_LOCATION_HEADER) {

    //  Run Function
    app("assets_manager")->unenqueue_style($name, $section);
}

//  Enqueue Script
function enqueue_script($name, $is_inline = false, $data = null, $section = VIEW_LOCATION_FOOTER, $attrs = array()) {

    //  Run Function
    app("assets_manager")->enqueue_script($name, $is_inline, $data, $section, $attrs);
}

//  Unenqueue Script
function unenqueue_script($name, $section = VIEW_LOCATION_HEADER) {

    //  Run Function
    app("assets_manager")->unenqueue_script($name, $section);
}

//  Set Meta
function setMeta($name, $content, $attrs = array()) {

    //  Run Function
    app("assets_manager")->setMeta($name, $content, $attrs);
}

//  Set Raw Meta
function setRawMeta($key, $val, $attrs = array()) {

    //  Run Function
    app("assets_manager")->setRawMeta($key, $val, $attrs);
}

//  Remove Meta
function removeMeta($key) {

    //  Run Function
    app("assets_manager")->removeMeta($key);
}

//  Get Style
function getStyle($name) {

    //  Run Function
    return app("assets_manager")->removeMeta($name);
}

//  Get Script
function getScript($name) {

    //  Run Function
    return app("assets_manager")->removeMeta($name);
}

//  Get Meta
function getMeta($key) {

    //  Run Function
    return app("assets_manager")->removeMeta($key);
}

//  Get Printable Styles
function printableStyles($section) {

    //  Run Function
    return app("assets_manager")->printableStyles($section);
}

//  Get Printable Scripts
function printableScripts($section) {

    //  Run Function
    return app("assets_manager")->printableScripts($section);
}

//  Get Printable Metas
function printableMetas() {

    //  Run Function
    return app("assets_manager")->printableMetas();
}

//  In Admin
function in_admin() {

    //  Run Function
    return app("assets_helper")->in_admin();
}

//  Route Info
function routeInfo() {

    //  Run Function
    return app("assets_helper")->routeInfo();
}

//  Enqueue Detected CSS
function enqueue_detected_css($routeInfo = null) {

    //  Run Function
    return app("assets_helper")->enqueue_detected_css($routeInfo);
}

//  Enqueue Detected JS
function enqueue_detected_js($routeInfo = null) {

    //  Run Function
    return app("assets_helper")->enqueue_detected_js($routeInfo);
}

//  Assets Path
function assetsPath($file = '') {

    //  Run Function
    return app("assets_helper")->assetsPath($file);
}

//  CSS Path
function cssPath($file = '') {

    //  Run Function
    return app("assets_helper")->cssPath($file);
}

//  JS Path
function jsPath($file = '') {

    //  Run Function
    return app("assets_helper")->jsPath($file);
}

//  Image Path
function imagePath($file = '') {

    //  Run Function
    return app("assets_helper")->imagePath($file);
}

//  Upload Path
function uploadPath($file = '') {

    //  Run Function
    return app("assets_helper")->uploadPath($file);
}

//  Module Assets Path
function moduleAssetsPath($file = '') {

    //  Run Function
    return app("assets_helper")->moduleAssetsPath($file);
}

//  Module CSS Path
function moduleCssPath($file = '') {

    //  Run Function
    return app("assets_helper")->moduleCssPath($file);
}

//  Module JS Path
function moduleJsPath($file = '') {

    //  Run Function
    return app("assets_helper")->moduleJsPath($file);
}

//  Module Image Path
function moduleImagePath($file = '') {

    //  Run Function
    return app("assets_helper")->moduleImagePath($file);
}

//  Package Assets Path
function packageAssetsPath($package, $file = '') {

    //  Run Function
    return app("assets_helper")->packageAssetsPath($package, $file);
}

//  Package CSS Path
function packageCssPath($package, $file = '') {

    //  Run Function
    return app("assets_helper")->packageCssPath($package, $file);
}

//  Package JS Path
function packageJsPath($package, $file = '') {

    //  Run Function
    return app("assets_helper")->packageJsPath($package, $file);
}

//  Package Image Path
function packageImagePath($package, $file = '') {

    //  Run Function
    return app("assets_helper")->packageImagePath($package, $file);
}

//  Assets URL
function assetsUrl($file = '') {

    //  Run Function
    return app("assets_helper")->assetsUrl($file);
}

//  CSS URL
function cssUrl($file = '') {

    //  Run Function
    return app("assets_helper")->cssUrl($file);
}

//  JS URL
function jsUrl($file = '') {

    //  Run Function
    return app("assets_helper")->jsUrl($file);
}

//  CSS URL
function imageUrl($file = '') {

    //  Run Function
    return app("assets_helper")->imageUrl($file);
}

//  Upload URL
function uploadUrl($file = '') {

    //  Run Function
    return app("assets_helper")->uploadUrl($file);
}

//  Module Assets URL
function moduleAssetsUrl($file = '') {

    //  Run Function
    return app("assets_helper")->moduleAssetsUrl($file);
}

//  Module CSS URL
function moduleCssUrl($file = '') {

    //  Run Function
    return app("assets_helper")->moduleCssUrl($file);
}

//  Module JS URL
function moduleJsUrl($file = '') {

    //  Run Function
    return app("assets_helper")->moduleJsUrl($file);
}

//  Module Image URL
function moduleImageUrl($file = '') {

    //  Run Function
    return app("assets_helper")->moduleImageUrl($file);
}

//  Package Assets URL
function packageAssetsUrl($package, $file = '') {

    //  Run Function
    return app("assets_helper")->packageAssetsUrl($package, $file);
}

//  Package CSS URL
function packageCssUrl($package, $file = '') {

    //  Run Function
    return app("assets_helper")->packageCssUrl($package, $file);
}

//  Package JS URL
function packageJsUrl($package, $file = '') {

    //  Run Function
    return app("assets_helper")->packageJsUrl($package, $file);
}

//  Package Image URL
function packageImageUrl($file = '') {

    //  Run Function
    return app("assets_helper")->packageImageUrl($package, $file);
}


///////////////////////////
//  Additional Helpers
///////////////////////////

function upload_file($key, $folder = "") {
    $fileName = Input::get($key);
    if(Input::hasFile($key)) {
        $file = Input::file($key);
        $newFilename = uniqid() . "_" . time() . "." . $file->getClientOriginalExtension();
        $file->move(uploadPath($folder), $newFilename);
        $fileName = $folder . $newFilename;
    }
    return $fileName;
}