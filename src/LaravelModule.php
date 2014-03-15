<?php namespace Developeryamhi\LaravelAdmin;

use Illuminate\Foundation\Application;

class LaravelModule extends \Illuminate\Support\ServiceProvider {

    //  Set Defer
    protected $defer = false;

    //	Name
    private $name;

    //	Version
    private $version;

    //	Module Name
    private $moduleName;

    //	Module Description
    private $moduleDescription;

    //	Module Path
    private $modulePath;

    //	Module Order
    private $order = 0;

    //  Module Locked
    private $locked = false;

    //	Module Dependency
    private $dependsOn = array();

    //	Meta Data
    private $metaData = array();

    //  Is Package
    private $is_package = false;

    //	Is Valid
    private $is_valid = true;

    //	Is Enabled
    private $is_enabled = false;

    //  Loaded From DB
    private $from_db = false;

    //	Application Instance
    protected $app;

    //	Module Item Instance
    protected $moduleItem;

    //	Actual Module Instance
    protected $module;


    /**
     * Initialize a module
     * @param Application $app
     */
    public function __construct(Application $app, $name, $path, $is_package = false, $read_meta = true, $load_from_db = false, $db_item = null) {

        //	Store App Instance
        $this->app = $app;

        //	Store Name
        $this->name = $name;

        //	Store Module Path
        $this->modulePath = str_ireplace("\\", "/", $path);

        //  Store Loaded from DB
        $this->from_db = $load_from_db;

        //	Read Meta Data
        if ($read_meta)
            $this->readMeta();

        //	Set Is Package
        if(!is_null($is_package))
            $this->is_package = (bool)intval($is_package);

        //  Check & Load Module Helper File Only
        if($this->app['laravel-modules']->getConfig('preload_helper'))
            $this->loadIncludes($this->app['laravel-modules']->getConfig('preload_helper'));

        //  Check Load From DB
        if($load_from_db)
            $this->loadFromDatabaseObject($db_item);
    }

    /**
     * Read Module Meta Data
     * @return void
     */
    public function readMeta() {

        //	Meta File
        $fileData = @json_decode($this->app['files']->get($this->modulePath($this->app['laravel-modules']->getConfig('meta_file')), true));

        //	Check Data
        if ($fileData) {

            //	Store Meta Data
            $this->version = (isset($fileData->version) ? $fileData->version : "alpha");
            $this->moduleName = (isset($fileData->name) ? $fileData->name : $this->name);
            $this->moduleDescription = (isset($fileData->description) ? $fileData->description : $this->name);
            $this->order = (isset($fileData->order) ? $fileData->order : 0);
            $this->locked = (isset($fileData->locked) ? (bool)intval($fileData->locked) : false);

            //	Store Dependency
            $dependency = array();
            if (isset($fileData->dependsOn)) {
                foreach ($fileData->dependsOn as $dKey => $dVer)
                    $dependency[$dKey] = $dVer;
            }
            $this->dependsOn = $dependency;

            //	Metadata
            $metaData = array();

            //	Loop Each Metadata
            foreach ($fileData as $key => $val) {

                //	Store Metadata
                $metaData[$key] = (is_object($val) ? (array) $val : $val);
            }

            //	Store Meta Data Instance
            $this->metaData = $metaData;
        } else {

            //	Set Invalid
            $this->is_valid = false;
        }
    }

    /**
     * Check Module Data is Valid
     * @return bool
     */
    public function isValid() {
        return $this->is_valid;
    }

    /**
     * Check Module is Enabled
     * @return bool
     */
    public function isEnabled() {
        return $this->is_enabled;
    }

    /**
     * Check Module is Locked
     * @return bool
     */
    public function isLocked() {
        return $this->locked;
    }

    /**
     * Check Module Exists Phusically
     */
    public function existsInFileSystem() {
        return ($this->app['files']->exists($this->modulePath));
    }

    /**
     * Check has Dependencies Enabled
     */
    public function hasDependenciesEnabled() {

        //  Enabled
        $enabled = (bool)$this->activate(false, true);

        //  If Enabled
        if($enabled) {

            //  Get Dependencies
            $dependencies = $this->def("dependsOn");

            //  Check for Dependencies
            if($dependencies) {

                //  Loop Each
                foreach(array_keys($dependencies) as $dependency) {

                    //  Locate Module
                    $dModule = $this->app["laravel-modules"]->module($dependency);

                    //  Check Path is Valid
                    if(!$dModule || ($dModule && !$dModule->existsInFileSystem())) {

                        //  Break
                        $enabled = false;
                        break;
                    }
                }
            }
        }

        return $enabled;
    }

    /**
     * Check is fine
     */
    public function isFine() {
        return ($this->isValid() && (!$this->isEnabled() || ($this->isEnabled() && $this->existsInFileSystem() && $this->hasDependenciesEnabled())));
    }

    /**
     * Check if Module is Register Ready
     */
    public function isRegisterReady() {
        return ($this->isValid() && $this->isEnabled() && $this->existsInFileSystem() && $this->hasDependenciesEnabled());
    }

    /**
     * Return name of module
     * @return string
     */
    public function name() {
        return $this->name;
    }

    /**
     * Return version of module
     * @return string
     */
    public function version() {
        return $this->version;
    }

    /**
     * Return Label Name of module
     * @return string
     */
    public function moduleName() {
        return $this->moduleName;
    }

    /**
     * Return description of module
     * @return string
     */
    public function description() {
        return $this->moduleDescription;
    }

    /**
     * Module path
     * @param  string $path
     * @return string
     */
    public function modulePath($path = null) {
        $base = str_ireplace("\\", "/", app()->make("path.base") . "/" . $this->moduleFolder());
        if ($path)
            return rtrim($base, '/') . '/' . ltrim($path, '/');
        else
            return rtrim($base, '/') . '/';
    }

    /**
     * Module Target Folder
     * @return void
     */
    public function moduleFolder($path = null) {
        if ($path)
            return rtrim($this->modulePath, '/') . '/' . ltrim($path, '/');
        else
            return rtrim($this->modulePath, '/') . '/';
    }

    /**
     * Module Dependency
     * @return void
     */
    public function dependsOn() {
        return $this->dependsOn;
    }

    /**
     * Module is from Package
     */
    public function isPackage() {
        return $this->is_package;
    }

    /**
     * Check Module is Registered in System
     */
    public function registeredInSystem() {
        return $this->from_db;
    }

    /**
     * Module Load Order
     * @return void
     */
    public function order() {
        return $this->order;
    }

    /**
     * Get Meta Value
     * @return mixed
     */
    public function def($key, $def = null) {
        if (isset($this->metaData[$key]))
            return $this->metaData[$key];
        return $def;
    }

    /**
     * Use Migration Fix
     */
    public function useMigrationFix() {
        return $this->def("migration_fix", false);
    }

    /**
     * Get Database Object of Module
     */
    public function dbObject() {
        return $this->moduleItem;
    }

    /**
     * Register module
     * @return void
     */
    public function register($only_package = false) {

        // Register module as a package
        $this->package('modules/' . $this->name, $this->name, rtrim($this->modulePath(), '/'));

        // Register service provider
        $this->registerProviders();

        //	Read Module
        $this->readModule();

        //  Load Includes
        $this->loadIncludes();

        //  Check is Register Ready
        if(!is_null($this->module) && $this->isRegisterReady()) {

            //  Run the Ready Function
            if(method_exists($this->module, "__whenReady"))
                call_user_func_array(array($this->module, "__whenReady"), array());

            //  Fire Module Ready
            $this->app["events"]->fire("module.ready", array($this->name, $this->module, $this));

            //  Fire Individual Module Ready
            $this->app["events"]->fire("module.ready." . $this->name, array($this->module, $this));
        }
    }

    /**
     * Load Module Includes
     */
    public function loadIncludes($load_only = null) {

        // Get files for inclusion
        $moduleInclude = $this->def("include", array());

        //	Check for no include
        if ($moduleInclude !== FALSE) {

            //	Get the inclusion list
            $globalInclude = $this->app['laravel-modules']->getConfig('include');
            $include = array_merge($globalInclude, $moduleInclude);

            //  Check Load Only
            if($load_only)
                $include = array($load_only);

            // Include all of them if they exist
            foreach ($include as $file) {
                $path = $this->modulePath($this->app['laravel-modules']->getConfig('includes_folder') . $file);
                if ($this->app['files']->exists($path))
                    require_once $path;
            }
        }
    }

    /**
     * Register service provider for module
     * @return void
     */
    public function registerProviders() {
        $providers = $this->def('provider');

        if ($providers) {
            if (is_array($providers)) {
                foreach ($providers as $provider) {
                    $this->app->register($instance = new $provider($this->app));
                }
            } else {
                $this->app->register($instance = new $providers($this->app));
            }
        }
    }

    /**
     * Read Module
     */
    public function readModule() {

        //	Check Already Read
        if (!$this->module) {

            //	Handler File
            $handler_file = $this->def('handler_file', $this->app['laravel-modules']->getConfig('handler_file'));

            //	Check fort Handler File
            if ($handler_file && $this->app["files"]->exists($this->modulePath($handler_file))) {

                //	Include File
                require_once $this->modulePath($handler_file);

                //	Handler Class
                $handler_class = $this->def('handler_class');

                //	Check Handler Class Exists
                if (class_exists($handler_class)) {

                    //	Init the Module Handler Class
                    $this->module = new $handler_class($this);

                    //  Assign Module Instance
                    $this->module->module = $this;

                    //  Assign App Instance
                    $this->module->app = $this->app;

                    //	Assign Module Name
                    $this->module->name = $this->name();

                    //	Run the Loaded Function
                    if (method_exists($this->module, "__whenLoaded"))
                        call_user_func_array(array($this->module, "__whenLoaded"), array());

                    //  Fire Module Ready
                    $this->app["events"]->fire("module.loaded", array($this->name, $this->module, $this));

                    //  Fire Individual Module Ready
                    $this->app["events"]->fire("module.loaded." . $this->name, array($this->module, $this));
                }
            }
        }
    }

    /**
     * Activate Module
     */
    public function activate($force = false, $check_only = false) {

        //	Check Module Item
        if ($this->moduleItem) {

            //	Check Already Activated
            if (!$check_only && $this->moduleItem->isActivated())
                return true;

            //	Get Dependency
            $passed_dependency = true;

            //	Messages
            $messages = array();

            //	Loop Each Dependency
            foreach ($this->dependsOn as $dModule => $dVersion) {

                //	Add Message
                $messages[$dModule] = array();

                //	Check Module Exists
                if (ModuleItem::moduleExists($dModule, $dVersion)) {

                    //	Check if Module is Activated
                    if (!ModuleItem::moduleEnabled($dModule)) {

                        //	Set Error
                        $passed_dependency = false;

                        //	Add Message
                        $messages[$dModule]["exists"] = true;
                        $messages[$dModule]["activated"] = false;
                        $messages[$dModule]["version_match"] = true;
                        $messages[$dModule]["version"] = ModuleItem::findModule($dModule)->version;
                        $messages[$dModule]["wanted_version"] = $dVersion;
                    } else {

                        //  Unset
                        unset($messages[$dModule]);
                    }
                } else {

                    //	Set Error
                    $passed_dependency = false;

                    //	Add Message
                    $messages[$dModule]["exists"] = false;
                    $messages[$dModule]["activated"] = false;
                    $messages[$dModule]["version_match"] = false;
                    $messages[$dModule]["wanted_version"] = $dVersion;

                    //	Check if Any Version is Available
                    if (ModuleItem::moduleExists($dModule)) {

                        //	Add Message
                        $messages[$dModule]["exists"] = true;
                        $messages[$dModule]["version"] = ModuleItem::findModule($dModule)->version;
                    }
                }
            }

            //	Check for Force
            if ($force)
                $passed_dependency = true;

            //	Check Valid
            if ($passed_dependency) {

                //	Passed Activation
                $passed_activation = true;

                //	Messages
                $messages2 = array();

                //	Check for Messages
                foreach ($messages as $dModule => $data) {

                    //	Check Module Exists
                    if ($data["exists"] && $data["version_match"]) {

                        //	Get The Required Module
                        $rModule = $this->app["laravel-modules"]->module($dModule);

                        //	Activate Module
                        $rModule->activate();

                        //	Run the Dependency Module Activation Hook
                        $rModule->runHook("activated");
                    } else {

                        //	Set Error
                        $passed_activation = false;

                        //	Set Message
                        $messages2[$dModule] = $data;
                    }
                }

                //	Check & Return
                if ($passed_activation) {

                    //  Check for Not Check Only
                    if(!$check_only) {

                        //	Activate Module
                        $this->is_enabled = true;
                        $this->moduleItem->activate();

                        //  Register Package so things are available from the Module
                        $this->register();

                        //	Run the Activation Hook
                        $this->runHook("activated");
                    }

                    //	Return
                    return true;
                } else {
                    return $messages2;
                }
            } else {
                return $messages;
            }
        }

        return null;
    }

    /**
     * Dectivate Module
     */
    public function deactivate() {

        //	Check Item
        if ($this->moduleItem) {

            //	Check Already Deactivated
            if (!$this->moduleItem->isActivated())
                return true;

            //  Check if Module is Locked
            if($this->isLocked())
                return false;

            //	Deactivate Module
            $this->is_enabled = false;
            $this->moduleItem->deactivate();

            //	Run the Deactivation Hook
            $this->runHook("deactivated");

            //	Return
            return true;
        }

        return null;
    }

    /**
     * Delete
     */
    public function delete() {

        //  Check for Locked
        if(!$this->isLocked() && !$this->isEnabled()) {

            //	Run the Deleted Hook
            $this->runHook("deleted");

            //	Try the Delete
            $this->app["files"]->deleteDirectory($this->modulePath());

            //	Delete the Database Object
            if ($this->moduleItem)
                $this->moduleItem->delete();

            return true;
        }

        return false;
    }

    /**
     * Sync Details to Database
     */
    public function syncToDatabase($force = false) {

        //	Get Info From Database
        $this->moduleItem = ModuleItem::findModule($this->name);

        //	Check if Exists
        if ($this->moduleItem) {

            //	Check Version Installed
            if ($force || !ModuleVersionItem::versionInstalled($this->name, $this->version)) {

                //	Update Data
                $this->moduleItem->version = $this->version;
                $this->moduleItem->name = $this->moduleName;
                $this->moduleItem->description = $this->moduleDescription;
                $this->moduleItem->depends_on = serialize($this->dependsOn);
                $this->moduleItem->meta_data = serialize($this->metaData);
                $this->moduleItem->order_index = $this->order;
                $this->moduleItem->locked = $this->locked;
                $this->moduleItem->save();

                //	Run the Installation Hook
                $this->runHook("updated");
            }

            //	Store Details from Database
            $this->loadFromDatabaseObject($this->moduleItem);
        } else {

            //	Check for Auto Order Index
            if ($this->order == "auto") {

                //	Get Last Item
                $last_item = ModuleItem::orderBy("order_index", "DESC")->limit(1)->get()->first();

                //	Set New Order
                $this->order = ($last_item ? $last_item->order_index + 1 : 0);
            }

            //	Insert New Data
            $this->moduleItem = new ModuleItem();
            $this->moduleItem->module = $this->name;
            $this->moduleItem->version = $this->version;
            $this->moduleItem->name = $this->moduleName;
            $this->moduleItem->description = $this->moduleDescription;
            $this->moduleItem->path = $this->modulePath;
            $this->moduleItem->depends_on = serialize($this->dependsOn);
            $this->moduleItem->meta_data = serialize($this->metaData);
            $this->moduleItem->is_package = $this->is_package;
            $this->moduleItem->order_index = $this->order;
            $this->moduleItem->locked = $this->locked;
            $this->moduleItem->save();

            //	Save Version Installed Informations
            ModuleVersionItem::versionInstalled($this->name, $this->version);

            //	Run the Installation Hook
            $this->runHook("installed");
        }

        //  Set Registered In System
        $this->from_db = true;
    }

    /**
     * Sync Info from the Module Meta File
     */
    public function syncFromMeta() {

        //	Read Meta
        $this->readMeta();

        //	Sync to Database
        $this->syncToDatabase(true);

        //  Fix Migrations
        if($this->hasMigrations())
            $this->addMigrationsAutoload(false);
        else
            $this->removeMigrationsAutoload(false);
    }

    /**
     * Run the seeder if it exists
     * @return void
     */
    public function seed() {

        //  Get Seeders
        $class = $this->def('seeder');

        //  Check Seeders Available
        if($class) {

            //  Confirm Array List
            if (!is_array($class))
                $class = array($class);

            //  Unguard Eloquent
            \Eloquent::unguard();

            //  Loop Each Seed
            foreach ($class as $c) {

                //  Confirm Class Exists
                if (class_exists($c)) {

                    //  Create Seeder Instance & Run
                    $seeder = new $c;
                    $seeder->run();
                }
            }

            //  Reguard Eloquent
            \Eloquent::reguard();
        }
    }

    /**
     * Do Seeding
     */
    public function doSeeding($version, $force = false) {

        //  Check for Seeds & Version
        if($this->def('seeder') && ($force || !ModuleVersionItem::versionInstalled($this->name . "::seeds", $version, false))) {

            //  Run Seeders
            $this->seed();

            //  Store Versioning Info
            ModuleVersionItem::versionInstalled($this->name . "::seeds", $version);
        }
    }

    /**
     * Resolve Migration Class Name
     */
    public function resolveMigrationClassName($name) {

        //  Detected Class Name
        $class_name = null;

        //  Match Class Name
        $cMatch = null;
        preg_match('/([0-9]+)_([0-9]+)_([0-9]+)_([0-9]+)_(.*)\.php/i', $name, $cMatch);

        //  Check Match
        if($cMatch) {

            //  Get the Name
            $name = $cMatch[5];

            //  Class Name
            $class_name = implode("", explode(" ", ucwords(implode(" ", explode("_", $name)))));
        }

        return $class_name;
    }

    /**
     * Check Module Migrations are done
     */
    public function migrationsDone($version = null) {

        //  Get Version
        $version || $version = ($this->module ? $this->module->migrate_version : 0);

        //  Check
        return ModuleVersionItem::versionInstalled($this->name . "::migrate", $version, false);
    }

    /**
     * Check Module has Migrations
     */
    public function hasMigrations() {

        //	Migrations Folder
        $mFolder = $this->modulePath("migrations");

        //	Check Folder Exists & Not Empty
        if ($this->app["files"]->exists($mFolder) && sizeof($this->app["files"]->files($mFolder)) > 0)
            return true;

        return false;
    }

    /**
     * Do Module Migrations
     */
    public function doMigrations($version, $force = false) {

        //  Check for Migration & Version
        if($this->hasMigrations() && ($force || !ModuleVersionItem::versionInstalled($this->name . "::migrate", $version, false))) {

            //  Get Migration Files
            $migrations = $this->app["files"]->files($this->modulePath("migrations"));

            //  Loop Each Migration
            foreach($migrations as $migration) {

                //  Load the File
                require_once $migration;

                //  Detect Class Name
                $mClass = $this->resolveMigrationClassName($migration);

                //  Check Class Name
                if($mClass) {

                    //  Create Instance
                    $ins = new $mClass();

                    //  Do Migration
                    $ins->up();
                }
            }

            //  Store Versioning Info
            ModuleVersionItem::versionInstalled($this->name . "::migrate", $version);
        }
    }

    /**
     * Undo Module Migrations
     */
    public function undoMigrations($version) {

        //  Check Migrations Exists
        if($this->hasMigrations()) {

            //  Get Migration Files
            $migrations = $this->app["files"]->files($this->modulePath("migrations"));

            //  Reverse List
            $migrations = array_reverse($migrations);

            //  Loop Each Migration
            foreach($migrations as $migration) {

                //  Load the File
                require_once $migration;

                //  Detect Class Name
                $mClass = $this->resolveMigrationClassName($migration);

                //  Check Class Name
                if($mClass) {

                    //  Create Instance
                    $ins = new $mClass();

                    //  Undo Migration
                    $ins->down();
                }
            }

            //  Remove Version Log
            ModuleVersionItem::removeVersionLog($this->name . "::migrate", $version);
        }
    }

    /**
     * Add Migration Fix
     */
    public function addMigrationsAutoload($autoload = true) {

        //  Check Uses Migration Fix
        if($this->useMigrationFix()) {

            //	Base Path
            $composer_path = app()->make("path.base") . "/composer.json";

            //	Composer JSON
            $composerJSON = @json_decode($this->app['files']->get($composer_path, true));

            //	Paths to Add
            $paths = array(
                $this->moduleFolder("migrations")
            );

            //	Loop Each Paths
            foreach ($paths as $path) {

                //	Check if Path doesn't Exists
                if (!in_array($path, $composerJSON->autoload->classmap)) {

                    //	Push to List
                    $composerJSON->autoload->classmap[] = $path;
                }
            }

            //	Save Output Composer File
            $this->app["files"]->put($composer_path, str_ireplace("\\/", "/", $this->app["laravel-modules"]->prettyJsonEncode($composerJSON)));

            //	Check Autoload
            if ($autoload) {

                //	Do Autoload
                $this->app["laravel-modules"]->doDumpAutoload();
            }
        }
    }

    /**
     * Remove Migration Fix
     */
    public function removeMigrationsAutoload($autoload = true) {

        //  Check Uses Migration Fix
        if($this->useMigrationFix()) {

            //	Base Path
            $composer_path = app()->make("path.base") . "/composer.json";

            //	Composer JSON
            $composerJSON = @json_decode($this->app['files']->get($composer_path, true));

            //	Paths to Add
            $paths = array(
                $this->moduleFolder("migrations")
            );

            //	Check if Path doesn't Exists
            foreach ($composerJSON->autoload->classmap as $i => $classmap) {

                //	Check in Array
                if (in_array($classmap, $paths)) {

                    //	Unset
                    unset($composerJSON->autoload->classmap[$i]);
                }
            }

            //	Save Output Composer File
            $this->app["files"]->put($composer_path, str_ireplace("\\/", "/", $this->app["laravel-modules"]->prettyJsonEncode($composerJSON)));

            //	Check Autoload
            if ($autoload) {

                //	Do Autoload
                $this->app["laravel-modules"]->doDumpAutoload();
            }
        }
    }

    /**
     * Load Module Data from Database
     */
    public function loadFromDatabaseObject($obj = null) {

        //  Search Object
        $obj || $obj = ModuleItem::findModule($this->name);

        //  Check
        if($obj) {

            //	Store Object
            $this->moduleItem = $obj;

            //	Store Details
            $this->moduleName = $obj->name;
            $this->moduleDescription = $obj->description;
            $this->version = $obj->version;
            $this->is_package = (bool) $obj->is_package;
            $this->order = (int) $obj->order_index;
            $this->is_enabled = (bool) $obj->enabled;
            $this->locked = (bool)intval($obj->locked);
            $this->dependsOn = unserialize($obj->depends_on);
            $this->metaData = unserialize($obj->meta_data);
        }
    }

    /**
     * Trigger Hooks to Handler Class
     */
    public function runHook($action) {

        //	Read Module
        $this->readModule();

        //	Check Handler Class Loaded
        if (!$this->module)
            return;

        //  Valid
        $valid = true;

        //	Switch Action
        switch (strtolower($action)) {

            //	Installed
            case "installed":

                //	Check Method Exists & Run
                if (method_exists($this->module, "__whenInstalled"))
                    call_user_func_array(array($this->module, "__whenInstalled"), array());

                //	Add Migrations to Autoload
                if ($this->isPackage() && $this->hasMigrations()) $this->addMigrationsAutoload();
                break;

            //	Updated
            case "updated":

                //	Check Method Exists & Run
                if (method_exists($this->module, "__whenUpdated"))
                    call_user_func_array(array($this->module, "__whenUpdated"), array());

                //	Add Migrations to Autoload
                if ($this->isPackage() && $this->hasMigrations()) $this->addMigrationsAutoload();
                break;

            //	Activated
            case "activated":

                //	Check Method Exists & Run
                if (method_exists($this->module, "__whenActivated"))
                    call_user_func_array(array($this->module, "__whenActivated"), array());

                //  Do Module Migrations
                //if ($this->hasMigrations()) $this->doMigrations();
                break;

            //	Deactivated
            case "deactivated":

                //	Check Method Exists & Run
                if (method_exists($this->module, "__whenDeactivated"))
                    call_user_func_array(array($this->module, "__whenDeactivated"), array());

                //  Undo Module Migrations
                //if ($this->hasMigrations()) $this->undoMigrations();
                break;

            //	Deleted
            case "deleted":

                //	Check Method Exists & Run
                if (method_exists($this->module, "__whenDeleted"))
                    call_user_func_array(array($this->module, "__whenDeleted"), array());

                //	Remove Migrations to Autoload
                if ($this->isPackage() && $this->hasMigrations()) $this->removeMigrationsAutoload();
                break;

            //  Default
            default:
                $valid = false;
                break;
        }

        //  Check for Valid
        if($valid) {

            //  Fire Module Ready
            $this->app["events"]->fire("module." . strtolower($action), array($this->name, $this->module, $this));

            //  Fire Individual Module Ready
            $this->app["events"]->fire("module." . strtolower($action) . "." . $this->name, array($this->module, $this));
        }
    }

    /**
     * Create Module Object from Database Object
     */
    public static function createInstance($app, $item) {

        //	Create Instance
        $module = new LaravelModule($app, $item->module, $item->path, null, false, true, $item);

        return $module;
    }

}
