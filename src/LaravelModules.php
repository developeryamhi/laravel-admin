<?php namespace Developeryamhi\LaravelAdmin;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;

class LaravelModules {

    /**
     * Modules collection
     * @var ModuleCollection
     */
    protected $modules;

    /**
     * IoC
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Database Version
     */
    protected $db_version = 1;

    //	Tables
    public $table_modules;
    public $table_module_versions;


    /**
     * Initialize the finder
     * @param Application $app
     */
    public function __construct(Application $app) {

        //  Store App Instance
        $this->app = $app;

        //  Reset List
        $this->modules = array();
    }

    /**
     * Get Config Value for Package
     * 
     * @param type $key
     */
    public function getConfig($key) {
        return $this->app["config"]->get("laravel-admin::module." . $key);
    }

    /**
     * Start finder
     * @return void
     */
    public function start() {

        //	Tables Names
        $this->table_modules = $this->getConfig("table_modules");
        $this->table_module_versions = $this->getConfig("table_module_versions");

        //	Install Database Tables
        $this->installTables();

        //	Load Modules List from Database
        $this->loadModulesFromDatabase();
    }

    /**
     * Register all modules in collection
     * @return void
     */
    public function register() {

        //  Register Auth Module
        $this->registerModule("auth-module", __DIR__ . '/modules/auth-module/')->activate();

        //  Register Settings Module
        $this->registerModule("settings-module", __DIR__ . '/modules/settings-module/')->activate();

        //	Process Modules
        foreach ($this->modules as $module) {

            //	Check if Module is Register Ready
            if ($module->isRegisterReady())
                $module->register();
        }
    }

    /**
     * Load Modules List From Database
     */
    public function loadModulesFromDatabase() {

        //	Reset List
        $this->modules = array();

        //	Modules List
        $items = ModuleItem::orderBy("order_index", "ASC")->get();

        //	Process List
        foreach ($items as $item) {

            //  Check if the Path of Module is Valid
            if($this->app["files"]->exists($item->path)) {

                //	Create Instance
                $module = LaravelModule::createInstance($this->app, $item);

                //  Checkif Module is Fine
                if($module->isFine()) {

                    //  Store Instance
                    $this->modules[$item->module] = $module;
                }
            }
        }

        //	Sort Modules List
        uasort($this->modules, array($this, 'sort_modules'));
    }

    /**
     * Scan Available Modules
     */
    public function scanModules($sync = false, $sync_list = null) {

        //	Scan Modules
        $this->scan();

        //	Sort Modules List
        uasort($this->modules, array($this, 'sort_modules'));

        //	Check Sync to Database
        if ($sync) {

            //	Process Modules
            foreach ($this->modules as $module) {

                //	Check
                if (!$sync_list || ($sync_list && in_array($module->name(), $sync_list))) {

                    //	Update Database Data to Module Items
                    $module->syncToDatabase();
                }
            }
        }
    }

    /**
     * Return module collection
     * @return ModuleCollection
     */
    public function modules() {
        return $this->modules;
    }

    /**
     * Check Has Module
     */
    public function hasModule($name) {
        return (isset($this->modules[$name]));
    }

    /**
     * Return single module
     * @param  string $name
     * @return Module
     */
    public function module($name) {
        if (isset($this->modules[$name]))
            return $this->modules[$name];
        return null;
    }

    /**
     * Get Module Paths
     */
    public function modulePaths() {

        //  Get the modules directory paths
        $modulesPaths = $this->getConfig('path');
        if (!is_array($modulesPaths))
            $modulesPaths = array($modulesPaths);

        //  Now prepare an array with all directories
        $paths = array();
        foreach ($modulesPaths as $modulesPath)
            $paths[$modulesPath] = $this->app['files']->directories(base_path($modulesPath));

        return $paths;
    }

    /**
     * Scan module folder and add valid modules to collection
     * @return array
     */
    public function scan() {

        //  Reset Modules List
        $this->modules = array();

        //  Now prepare an array with all directories
        $paths = $this->modulePaths();

        //  Check for Paths
        if ($paths) {

            //  Loop Each Paths
            foreach ($paths as $path => $directories) {

                //  Check has Directories
                if ($directories) {

                    //  Loop Each Directories
                    foreach ($directories as $directory) {

                        // Check if dir contains a module definition file
                        if ($this->app['files']->exists($directory . '/' . $this->getConfig('meta_file'))) {

                            //  Get Module Name
                            $name = pathinfo($directory, PATHINFO_BASENAME);

                            //  Create Module Instance
                            $thisModule = new LaravelModule($this->app, $name, $path . "/" . $name);

                            //  Check if Module is Valid & Store Instance
                            if ($thisModule->isValid()) {

                                //  Load Data from Database if Exists
                                $thisModule->loadFromDatabaseObject();

                                //  Store
                                $this->modules[$name] = $thisModule;
                            }
                        }
                    }
                }
            }
        }

        return $this->modules;
    }

    /**
     * Reset System
     */
    public function resetEverything() {

        //	Remove Migrtion Fixes for Modules
        foreach ($this->modules as $module)
            $module->removeMigrationsAutoload(false);

        //	Reset Tables
        $this->installTables(true);

        //	Do Autoload
        $this->doDumpAutoload();
    }

    /**
     * Install Database Tables
     */
    public function installTables($reset = false) {

        //	Check Reset
        if ($reset) {

            //	Reset Data
            Schema::dropIfExists($this->table_modules);
            Schema::dropIfExists($this->table_module_versions);
        }

        //	Check Table Already Exists
        if (!Schema::hasTable($this->table_module_versions)) {

            //	Install Table
            Schema::create($this->table_module_versions, function($table) {
                $table->increments('id');

                $table->string("name");
                $table->string("version");

                $table->timestamp('installed_on');
            });
        }

        //	Check Table Already Exists
        if (!Schema::hasTable($this->table_modules)) {

            //	Install Table
            Schema::create($this->table_modules, function($table) {
                $table->increments('id');

                $table->string("module");
                $table->string("version");
                $table->string("name");
                $table->string("description");
                $table->text("path");
                $table->text('depends_on');
                $table->text('meta_data');
                $table->integer("is_package")->default(0);
                $table->integer("order_index");
                $table->integer("enabled")->default(0);
                $table->integer("locked")->default(0);

                $table->timestamps();
            });
        }

        //	Set Existing Database Version
        ModuleVersionItem::versionInstalled("__system__", $this->db_version);
    }

    /**
     * Sort Modules
     */
    public function sort_modules($a, $b) {
        return $a->order() > $b->order();
    }

    /**
     * Prettify a JSON Encode ( PHP 5.4+ )
     * @param  mixed $values
     * @return string
     */
    public function prettyJsonEncode($values) {
        return version_compare(PHP_VERSION, '5.4.0', '>=') ? json_encode($values, JSON_PRETTY_PRINT) : json_encode($values);
    }

    /**
     * Dump Autoload
     */
    public function doDumpAutoload($dir = null, $extra = null) {
        $dir || $dir = "/";
        $base_path = app()->make("path.base") . "/" . trim($dir, "/") . "/";
        $composer = new Composer($this->app['files'], $base_path);
        return $composer->dumpAutoloads($extra);
    }

    /**
     * Dump Autoload Optimized
     */
    public function doDumpAutoloadOptimized($dir = null, $extra = null) {
        return $this->doDumpAutoload($dir, "-o " . $extra);
    }

    /**
     * Register Module from Another Package
     */
    public function registerModule($name, $dir) {

        //  Module
        $module = null;

        //  Check if Already Exists
        if(!$this->module($name)) {

            //	Generate Module
            $module = new LaravelModule($this->app, $name, ltrim($dir, app()->make("path.base")));

            //	Sync to Database
            $module->syncToDatabase();
        }

        //  Check for Module
        if($module) {

            //  Re-sort Things
            uasort($this->modules, array($this, 'sort_modules'));
        } else {

            //  Get Modules
            $module = $this->module($name);
        }

        return $module;
    }

    /**
     * Register Module from Another Package
     */
    public function registerModuleFromPackage($name, $dir) {

        //  Module
        $module = null;

        //  Check if Already Exists
        if(!$this->module($name)) {

            //	Generate Module
            $module = new LaravelModule($this->app, $name, ltrim($dir, app()->make("path.base")), true);

            //	Sync to Database
            $module->syncToDatabase();
        }

        //  Check for Module
        if($module) {

            //  Re-sort Things
            uasort($this->modules, array($this, 'sort_modules'));
        }

        return $module;
    }

}