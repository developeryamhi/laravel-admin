<?php namespace Developeryamhi\LaravelAdmin;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Application;

class LaravelAdmin {

    //  App Instance
    private $app;

    //  Themes to Use
    private $themesToUse = array();

    //  Styles to Use
    private $stylesToUse = array();

    //  Scripts to Use
    private $scriptsToUse = array();

    //  Registered Themes
    private $registeredThemes = array();

    //  Registered Styles
    private $registeredStyles = array();

    //  Registered Scripts
    private $registeredScripts = array();


    /**
     * Initialize the Laravel Admin
     * @param Application $app
     */
    public function __construct(Application $app) {

        //  Store App Instance
        $this->app = $app;

        //  Clear Everything
        $this->clearRegisteredThemes();
        $this->clearRegisteredStyles();
        $this->clearRegisteredScripts();

        //  Register Laravel Modules Scanner
        $this->app->instance("laravel-modules", new LaravelModules($this->app));
    }

    /**
     * Get Config Value for Package
     * 
     * @param type $key
     */
    public function getConfig($key) {
        return $this->app["config"]->get("laravel-admin::" . $key);
    }

    /**
     * Start
     */
    public function start() {

        //  Start Modules
        $this->app["laravel-modules"]->start();
    }

    /**
     * Register
     */
    public function register() {

        //  Registered Default Themes
        $this->registerTheme("admin", "default", __DIR__ . "/base_assets/admin_theme.css");
        $this->registerTheme("guest", "default", __DIR__ . "/base_assets/admin_theme.css");
        $this->registerTheme("error", "default", __DIR__ . "/base_assets/admin_theme.css");

        //  Registered Default Styles
        $this->registerStyle("admin", "default", __DIR__ . "/base_assets/admin_style.css");
        $this->registerStyle("guest", "admin", __DIR__ . "/base_assets/admin_style.css");
        $this->registerStyle("guest", "default", __DIR__ . "/base_assets/guest_style.css");
        $this->registerStyle("error", "admin", __DIR__ . "/base_assets/admin_style.css");
        $this->registerStyle("error", "default", __DIR__ . "/base_assets/guest_style.css");

        //  Set Themes to Use
        $this->setThemeToUse("admin", "default");
        $this->setThemeToUse("guest", "default");
        $this->setThemeToUse("error", "default");

        //  Set Styles to Use
        $this->setStyleToUse("admin", "default");
        $this->setStyleToUse("guest", "admin");
        $this->setStyleToUse("guest", "default");
        $this->setStyleToUse("error", "admin");
        $this->setStyleToUse("error", "default");

        //  Register Scripts to Use
        $this->registerScript("admin", "default", __DIR__ . "/base_assets/admin.js");
        $this->registerScript("guest", "default", __DIR__ . "/base_assets/guest.js");
        $this->registerScript("error", "default", __DIR__ . "/base_assets/guest.js");

        //  Setup Setup
        $this->runSetup();

        //  Run Modules Register
        $this->app['laravel-modules']->register();
    }

    /**
     * Run Setup
     */
    public function runSetup() {

        //  Add Required Routes
        $this->_registerRoutes();

        //  Load Admin Setup Script
        require_once __DIR__ . '/includes/setup.php';
    }

    /**
     * Register Required Routes
     */
    private function _registerRoutes() {

        //  Load Routes File
        require_once __DIR__ . '/includes/routes.php';

        //  Add Route for Admin Theme
        Route::get('admin_theme.css', array("as" => $this->adminThemeUrlRoute(), "uses" => function() {

            //  Get Paths for Themes to Use
            $themes = getAdminThemesToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($themes, 'text/css');
        }));

        //  Add Route for Admin Style
        Route::get('admin_style.css', array("as" => $this->adminStyleUrlRoute(), "uses" => function() {

            //  Get Paths for Styles to Use
            $styles = getAdminStylesToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($styles, 'text/css');
        }));

        //  Add Route for Admin Script
        Route::get('admin_script.js', array("as" => $this->adminScriptUrlRoute(), "uses" => function() {

            //  Get Paths for Scripts to Use
            $scripts = getAdminScriptsToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($scripts, 'text/javascript');
        }));

        //  Add Route for Guest Theme
        Route::get('guest_theme.css', array("as" => $this->guestThemeUrlRoute(), "uses" => function() {

            //  Get Paths for Themes to Use
            $themes = getGuestThemesToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($themes, 'text/css');
        }));

        //  Add Route for Guest Style
        Route::get('guest_style.css', array("as" => $this->guestStyleUrlRoute(), "uses" => function() {

            //  Get Paths for Styles to Use
            $styles = getGuestStylesToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($styles, 'text/css');
        }));

        //  Add Route for Guest Script
        Route::get('guest_script.js', array("as" => $this->guestScriptUrlRoute(), "uses" => function() {

            //  Get Paths for Scripts to Use
            $scripts = getGuestScriptsToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($scripts, 'text/javascript');
        }));

        //  Add Route for Error Theme
        Route::get('error_theme.css', array("as" => $this->errorThemeUrlRoute(), "uses" => function() {

            //  Get Paths for Themes to Use
            $themes = getErrorThemesToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($themes, 'text/css');
        }));

        //  Add Route for Error Style
        Route::get('error_style.css', array("as" => $this->errorStyleUrlRoute(), "uses" => function() {

            //  Get Paths for Styles to Use
            $styles = getErrorStylesToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($styles, 'text/css');
        }));

        //  Add Route for Error Script
        Route::get('error_script.js', array("as" => $this->errorScriptUrlRoute(), "uses" => function() {

            //  Get Paths for Scripts to Use
            $scripts = getErrorScriptsToUse();

            //  Return Response
            return LaravelAdmin::makeCombinedResponseContent($scripts, 'text/javascript');
        }));
    }

    /**
     * Make Combined Response Contents
     * 
     * @param type $path
     * @param type $content_type
     * @return type
     */
    public static function makeCombinedResponseContent($paths, $content_type, $status = 200) {

        //  Check Array
        if(!is_array($paths))   $paths = array($paths);

        //  Clear Contents
        clean_ob_contents();

        //  Output
        $output = "";

        //  Loop Each
        foreach($paths as $path) {

            //  Ob Start
            ob_start();

            //  Load File Data
            if(app("files")->exists($path))
                app("files")->getRequire($path);

            //  Get File Contents
            $contents = ob_get_clean();

            //  Append Contents
            $output .= $contents . PHP_EOL . PHP_EOL . PHP_EOL;
        }

        //  Make Response
        $response = Response::make($output, $status);

        //  Set Content Type
        $response->header('Content-Type', $content_type);

        //  Return Response
        return $response;
    }

    /**
     * Get Admin Alias
     */
    public function alias() {
        return app("config")->get("laravel-admin::prefix_admin");
    }

    /**
     * Admin Prepend Path
     */
    public function aliasPath() {

        //  Get Alias
        $alias = $this->alias();

        //  Admin Alias Path
        $prepend = ($alias ? $alias . '/' : '');

        return $prepend;
    }

    /**
     * Get Admin Nav Group
     */
    public function navGroup() {
        return $this->app["config"]->get("laravel-admin::nav_group");
    }

    /**
     * Get Admin Theme Route Name
     */
    public function adminThemeUrlRoute() {
        return $this->app["config"]->get("laravel-admin::admin_theme_url_route");
    }

    /**
     * Get Admin Style Route Name
     */
    public function adminStyleUrlRoute() {
        return $this->app["config"]->get("laravel-admin::admin_style_url_route");
    }

    /**
     * Get Admin Script Route Name
     */
    public function adminScriptUrlRoute() {
        return $this->app["config"]->get("laravel-admin::admin_script_url_route");
    }

    /**
     * Get Guest Theme Route Name
     */
    public function guestThemeUrlRoute() {
        return $this->app["config"]->get("laravel-admin::guest_theme_url_route");
    }

    /**
     * Get Guest Style Route Name
     */
    public function guestStyleUrlRoute() {
        return $this->app["config"]->get("laravel-admin::guest_style_url_route");
    }

    /**
     * Get Guest Script Route Name
     */
    public function guestScriptUrlRoute() {
        return $this->app["config"]->get("laravel-admin::guest_script_url_route");
    }

    /**
     * Get Error Theme Route Name
     */
    public function errorThemeUrlRoute() {
        return $this->app["config"]->get("laravel-admin::error_theme_url_route");
    }

    /**
     * Get Guest Style Route Name
     */
    public function errorStyleUrlRoute() {
        return $this->app["config"]->get("laravel-admin::error_style_url_route");
    }

    /**
     * Get Guest Script Route Name
     */
    public function errorScriptUrlRoute() {
        return $this->app["config"]->get("laravel-admin::error_script_url_route");
    }

    /**
     * Get Active Themes
     */
    public function getThemesToUse($group) {

        //  Themes
        $themes = array();

        //  Loop Each
        foreach($this->themesToUse[$group] as $theme) {

            //  Store
            $themes[$theme] = $this->registeredThemes[$group][$theme];
        }

        return $themes;
    }

    /**
     * Get Active Styles
     */
    public function getStylesToUse($group) {

        //  Styles
        $styles = array();

        //  Loop Each
        foreach($this->stylesToUse[$group] as $style) {

            //  Store
            $styles[$style] = $this->registeredStyles[$group][$style];
        }

        return $styles;
    }

    /**
     * Get Active Scripts
     */
    public function getScriptsToUse($group) {
        return $this->registeredScripts[$group];
    }

    /**
     * Get Active Style Path
     */
    public function stylePath() {

        //  Check for Style to Use
        if($this->styleToUse) {

            //  Return
            return $this->registeredStyles[$this->styleToUse];
        }

        return null;
    }

    /**
     * Register Theme
     * 
     * @param string $name
     * @param string $path
     */
    public function registerTheme($group, $name, $path) {

        //  Check Path Exists
        if(app("files")->exists($path)) {

            //  Register
            $this->registeredThemes[$group][$name] = str_ireplace("\\", "/", $path);
        }
    }

    /**
     * Unregister Theme
     * 
     * @param string $name
     */
    public function unregisterTheme($group, $name) {

        //  Unregister
        unset($this->registeredThemes[$group][$name]);
    }

    /**
     * Register Style
     * 
     * @param string $name
     * @param string $path
     */
    public function registerStyle($group, $name, $path) {

        //  Check Path Exists
        if(app("files")->exists($path)) {

            //  Register
            $this->registeredStyles[$group][$name] = str_ireplace("\\", "/", $path);
        }
    }

    /**
     * Unregister Style
     * 
     * @param string $name
     */
    public function unregisterStyle($group, $name) {

        //  Unregister
        unset($this->registeredStyles[$group][$name]);
    }

    /**
     * Register Script
     * 
     * @param string $name
     * @param string $path
     */
    public function registerScript($group, $name, $path) {

        //  Check Path Exists
        if(app("files")->exists($path)) {

            //  Register
            $this->registeredScripts[$group][$name] = str_ireplace("\\", "/", $path);
        }
    }

    /**
     * Unregister Script
     * 
     * @param string $name
     */
    public function unregisterScript($group, $name) {

        //  Unregister
        unset($this->registeredScripts[$group][$name]);
    }

    /**
     * Set Theme to Use
     * 
     * @param string $name
     */
    public function setThemeToUse($group, $name, $clear = false) {

        //  Check Exists
        if(isset($this->registeredThemes[$group][$name])) {

            //  Check for Clear
            if($clear)  $this->themesToUse[$group] = array();

            //  Store
            $this->themesToUse[$group][] = $name;
        }
    }

    /**
     * Set Style to Use
     * 
     * @param string $name
     */
    public function setStyleToUse($group, $name, $clear = false) {

        //  Check Exists
        if(isset($this->registeredStyles[$group][$name])) {

            //  Check for Clear
            if($clear)  $this->stylesToUse[$group] = array();

            //  Store
            $this->stylesToUse[$group][] = $name;
        }
    }

    /**
     * Clear Registered Themes
     */
    public function clearRegisteredThemes($group = null) {

        //  Check Group
        if($group) {

            //  Clear
            $this->registeredThemes[$group] = array();
        } else {

            //  Clear All
            $this->registeredThemes["admin"] = array();
            $this->registeredThemes["guest"] = array();
            $this->registeredThemes["error"] = array();
        }

        //  Run Destroy
        $this->destroyThemes($group);
    }

    /**
     * Clear Registered Styles
     */
    public function clearRegisteredStyles($group = null) {

        //  Check Group
        if($group) {

            //  Clear
            $this->registeredStyles[$group] = array();
        } else {

            //  Clear All
            $this->registeredStyles["admin"] = array();
            $this->registeredStyles["guest"] = array();
            $this->registeredStyles["error"] = array();
        }

        //  Run Destroy
        $this->destroyStyles($group);
    }

    /**
     * Clear Registered Scripts
     */
    public function clearRegisteredScripts($group = null) {

        //  Check Group
        if($group) {

            //  Clear
            $this->registeredScripts[$group] = array();
        } else {

            //  Clear All
            $this->registeredScripts["admin"] = array();
            $this->registeredScripts["guest"] = array();
            $this->registeredScripts["error"] = array();
        }

        //  Run Destroy
        $this->destroyScripts($group);
    }

    /**
     * Set No Themes
     */
    public function destroyThemes($group = null) {

        //  Check Group
        if($group) {

            //  Set Empty
            $this->themesToUse[$group] = array();
        } else {

            //  Set Empty
            $this->themesToUse["admin"] = array();
            $this->themesToUse["guest"] = array();
            $this->themesToUse["error"] = array();
        }
    }

    /**
     * Set No Styles
     */
    public function destroyStyles($group = null) {

        //  Check Group
        if($group) {

            //  Set Empty
            $this->stylesToUse[$group] = array();
        } else {

            //  Set Empty
            $this->stylesToUse["admin"] = array();
            $this->stylesToUse["guest"] = array();
            $this->stylesToUse["error"] = array();
        }
    }

    /**
     * Set No Scripts
     */
    public function destroyScripts($group = null) {

        //  Check Group
        if($group) {

            //  Set Empty
            $this->scriptsToUse[$group] = array();
        } else {

            //  Set Empty
            $this->scriptsToUse["admin"] = array();
            $this->scriptsToUse["guest"] = array();
            $this->scriptsToUse["error"] = array();
        }
    }

    /**
     * Log a debug message
     * @param  string $message
     * @return void
     */
    public function logDebug($message) {
        $this->log($message);
    }

    /**
     * Log an error message
     * @param  string $message
     * @return void
     */
    public function logError($message) {
        $this->log($message, 'error');
    }

    /**
     * Log an exception
     * @param  string $e
     * @return void
     */
    public function logException($e, $prepend = '') {
        $this->log($prepend . ' ' . $e->getMessage() . ' on line ' . $e->getLine() . ' at file ' . $e->getFile(), 'error');
    }

    /**
     * Log a message
     * @param  string $type
     * @param  string $message
     * @return void
     */
    public function log($message, $type = 'debug') {
        if ($this->app['config']->get('laravel-admin::debug')) {
            $namespace = 'LARAVEL-ADMIN';
            $message = "[$namespace] $message";

            if ($type == 'error')
                $this->app['log']->error($message);
            else
                $this->app['log']->debug($message);
        }
    }
}