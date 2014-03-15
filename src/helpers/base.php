<?php

/**
 * Set Message & Redirect
 */
function setMessageRedirect($message, $route, $type = FLASH_MSG_SUCCESS, $params = array()) {
    return Redirect::route($route, $params)
                ->with($type, $message);
}

/**
 * Set Error Message & Redirect
 */
function setErrorMessageRedirect($message, $route, $params = array()) {
    return setMessageRedirect($message, $route, FLASH_MSG_ERROR, $params);
}

/**
 * Set Warning Message & Redirect
 */
function setWarningMessageRedirect($message, $route, $params = array()) {
    return setMessageRedirect($message, $route, FLASH_MSG_WARNING, $params);
}

/**
 * Set Info Message & Redirect
 */
function setInfoMessageRedirect($message, $route, $params = array()) {
    return setMessageRedirect($message, $route, FLASH_MSG_INFO, $params);
}

/**
 * Set Success Message & Redirect
 */
function setSuccessMessageRedirect($message, $route, $params = array()) {
    return setMessageRedirect($message, $route, FLASH_MSG_SUCCESS, $params);
}

/**
 * Module is Active Helper
 */
function moduleIsActive($module) {
    $module = \Developeryamhi\LaravelAdmin\ModuleItem::findModule($module);
    if($module && $module->isActivated())
        return true;
    return false;
}

/**
 * CHMOD Recursive
 */
function chmod_r($path, $file_perm = 0755, $dir_perm = 0755) {
    $dp = opendir($path);
    while ($file = readdir($dp)) {
        if ($file != "." && $file != "..") {
            $pathNow = $path . "/" . $file;
            if (is_dir($pathNow)) {
                chmod($pathNow, $dir_perm);
                chmod_r($pathNow, $file_perm, $dir_perm);
            } else {
                chmod($pathNow, $file_perm);
            }
        }
    }
    closedir($dp);
    chmod($path, $dir_perm);
}

/**
 * Get ZIP Files
 */
function getZipFiles($zipPath) {
    $files = array();
    $zip = zip_open($zipPath);
    if ($zip) {
        while ($zip_entry = zip_read($zip)) {
            $files[] = zip_entry_name($zip_entry);
        }
    }
    return $files;
}

/**
 * Extract ZIP File
 */
function extractZip($zipPath, $extractTo, $appendPath = '', $perm = 0755) {
    $zip = new ZipArchive();
    if ($zip->open($zipPath) === TRUE) {
        $zip->extractTo($extractTo);
        $zip->close();

        chmod_r($extractTo . $appendPath, $perm, $perm);

        return true;
    }
    return false;
}

/**
 * Merge Event Fire Response Array
 */
function mergeEventFireResponseArray($original, $response) {
    $output = array();
    if($response) {
        foreach($response as $res)
            $output = array_merge($output, $res);
    } else {
        $output = $original;
    }
    return $output;
}

/**
 * Merge Event FIre Response
 */
function mergeEventFireResponse($original, $response) {
    $output = $original;
    if($response) {
        $output = end($response);
    }
    return $output;
}

/**
 * Get IP Address
 */
function ip_address() {
    $detected_ip = null;
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $detected_ip = trim($ip);
                break;
            }
        }
        if($detected_ip)
            break;
    }
    return $detected_ip;
}

/**
 * URL Safe Text
 */
function url_safe_text($str) {
    return str_ireplace(array(" ", "_", "&"), array("-", "-", "and"), strtolower($str));
}

/**
 * Run dump-autoload
 */
function composer_dump_autoload($dir = null, $extra = null) {
    return app("laravel-modules")->doDumpAutoload($dir, $extra);
}

/**
 * Run dump-autoload -o
 */
function composer_dump_autoload_optimized($dir = null, $extra = null) {
    return app("laravel-modules")->doDumpAutoloadOptimized($dir, $extra);
}

/**
 * Get Last Query
 */
function last_query() {
    $queries = DB::getQueryLog();
    $last_query = end($queries);
    return $last_query;
}

/**
 * Get Contents using cUrl
 */
function get_curl_contents($url, $timeout = 5) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 * Get cUrl Contents Plus Crawler
 */
function get_curl_contents_crawler($url, $timeout = 5) {
    $contents = get_curl_contents($url, $timeout);
    return new \Symfony\Component\DomCrawler\Crawler($contents);
} 