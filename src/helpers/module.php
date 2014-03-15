<?php

/**
 * Clean OB Contents
 */
function clean_ob_contents() {
    $level = ob_get_level();
    while($level > 0) {
        @ob_clean();
        $level--;
    }
}

/**
 * Add New Module
 */
function addNewModule($moduleName, $importFile) {
    $modulePaths = app("laravel-modules")->modulePaths();
    foreach(array_keys($modulePaths) as $dir) {
        $targetDir = realpath($dir) . "/" . $moduleName . "/";
        if(!file_exists($targetDir)) {
            extractZip($importFile, $targetDir);
            return true;
        }
        break;
    }
    return false;
}

/**
 * Check Has Module
 * 
 * @param type $name
 * @return type
 */
function hasTheModule($name) {
    return app("laravel-modules")->hasModule($name);
}

/**
 * Get Module
 * 
 * @param type $name
 * @return type
 */
function getTheModule($name) {
    return app("laravel-modules")->module($name);
}

/**
 * Get The Modules List
 * 
 * @return type
 */
function getTheModules() {
    return app("laravel-modules")->modules();
}

/**
 * Scan the Modules
 * 
 * @param type $sync
 * @param type $sync_list
 * @return type
 */
function scanTheModules($sync, $sync_list = null) {
    return app("laravel-modules")->scanModules($sync, $sync_list);
}

/**
 * Register the Module from Package
 */
function registerModuleFromPackage($name, $dir) {

    //	Register Package as Module
    return app("laravel-modules")->registerModuleFromPackage($name, $dir);
}

/**
 * Get Config Value Helper for Module
 */
function getModuleConfig($key, $def = null, $levels = 3) {

    //  Backtrace
    $backtrace = debug_backtrace();

    //  Callee
    $callee = array_shift($backtrace);

    //  Module Name
    $module_name = Cache::get("module_resolver_" . $callee["file"]);

    //  Check Name not Found
    if(!$module_name) {

        //  Search for First Case
        $mMatch = null;
        preg_match('/\/(.*)\-module\//i', $callee["file"], $mMatch);

        //  Check for Match
        if($mMatch) {

            //  Explode the Match
            $explodes = explode("/", $mMatch[1]);

            //  Store Module Name
            $module_name = end($explodes) . '-module';
        }

        //  Check Name not Found
        if(!$module_name && $levels > 0) {

            //  Get Path
            $path = dirname(substr($callee["file"], strlen(app()->make("path.base") . "/")));

            //  Search
            while($levels > 0) {

                //  Search for Module
                $module = Developeryamhi\LaravelAdmin\ModuleItem::where("path", $path)->first();

                //  Check
                if($module) {

                    //  Get Module Name
                    $module_name = $module->module;

                    break;
                } else {

                    //  Get Parent
                    $path = dirname($path);
                }

                //  Dec
                $levels--;
            }
        }

        //  Check Found
        if($module_name) {

            //  Cache the Data
            Cache::forever("module_resolver_" . $callee["file"], $module_name);
        }
    }

    //  Return
    return ($module_name ? Config::get($module_name . "::" . $key, $def) : $def);
}