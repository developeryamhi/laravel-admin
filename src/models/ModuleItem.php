<?php namespace Developeryamhi\LaravelAdmin;

class ModuleItem extends \Developeryamhi\LaravelAdmin\Base\Eloquent {

    public function __construct(array $attributes = array()) {
        $this->table = app("laravel-modules")->table_modules;
        parent::__construct($attributes);
    }

    public static function findModule($module) {
        return self::where("module", $module)->first();
    }

    public static function moduleExists($module, $version = null) {
        $q = ModuleItem::where("module", $module);
        if (!is_null($version)) {
            $version = str_ireplace(array(".*", ".x"), "+", $version);
            if (substr($version, -1) == "+")
                $q->where("version", ">=", ((float) substr($version, 0, -1)));
            else if (intval($version) == $version)
                $q->where("version", $version);
            else {
                if ($version != "*")
                    $q->where("version", $version);
            }
        }
        return ($q->first() ? true : false);
    }

    public static function moduleEnabled($module) {
        return (self::where("module", $module)->first()->enabled == "1");
    }

    public function activate() {
        $this->enabled = 1;
        $this->save();
    }

    public function deactivate() {
        $this->enabled = 0;
        $this->save();
    }

    public function isActivated() {
        return ($this->enabled == "1");
    }

    public function isLocked() {
        return ($this->locked == "1");
    }

    public function lockedText() {
        return ($this->locked == 1 ? "Yes" : "No");
    }

    public function hasDependencies() {
        $dependsOn = unserialize($this->depends_on);
        return ($dependsOn && sizeof($dependsOn) > 0);
    }

    public function dependencyTexts() {
        $texts = array();
        $dependsOn = unserialize($this->depends_on);
        if($dependsOn) {
            foreach($dependsOn as $depend_module => $depend_version) {
                $mod = app("laravel-modules")->module($depend_module);
                $texts[] = ($mod ? $mod->moduleName() : $depend_module) . " v." . $depend_version;
            }
        }
        return $texts;
    }

    public static function activationResponseFormatted($response) {

        //  Messages
        $messages = array();

        //	Loop Each
        foreach ($response as $dModule => $dData) {

            //	Get Error
            if (!$dData["exists"])
                $messages[] = "Dependency Module [{$dModule}] does not exist in the system.";
            else if ($dData["exists"] && !$dData["version_match"])
                $messages[] = "Dependency Module [{$dModule}] version [{$dData['wanted_version']}] required but [{$dData['version']}] exists.";
            else if ($dData["exists"] && !$dData["activated"])
                $messages[] = "Dependency Module [{$dModule}] is not activated. Try Forced Activation to activate the dependencies";
        }

        return $messages;
    }

    public static function activationResponseTerminal($module, $response) {

        //  Messages
        $messages = array();

        //	Loop Each
        foreach ($response as $dModule => $dData) {

            //	Get Error
            if (!$dData["exists"])
                $messages[] = "{$module->name()}: Dependency Module [{$dModule}] does not exist in the system.";
            else if ($dData["exists"] && !$dData["version_match"])
                $messages[] = "{$module->name()}: Dependency Module [{$dModule}] version [{$dData['wanted_version']}] required but [{$dData['version']}] exists.";
            else if ($dData["exists"] && !$dData["activated"])
                $messages[] = "{$module->name()}: Dependency Module [{$dModule}] is not activated. Try --force=true switch to activate the dependencies";
        }

        return $messages;
    }
}
