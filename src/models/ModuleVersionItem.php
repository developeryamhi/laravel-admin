<?php namespace Developeryamhi\LaravelAdmin;

class ModuleVersionItem extends \Developeryamhi\LaravelAdmin\Base\Eloquent {

    public function __construct(array $attributes = array()) {
        $this->table = app("laravel-modules")->table_module_versions;
        parent::__construct($attributes);
    }

    public static function findModule($name) {
        return self::where("name", $name)->first();
    }

    public static function versionInstalled($name, $version, $create = true) {

        //	Get Existing Database Version
        $existing_version = self::findModule($name);

        //	Check for Database Version Match
        if (!$existing_version || ($existing_version && $existing_version->versionRequiresUpdate($version))) {

            //	Check No Version Info
            if (!$existing_version) {

                //	New Instance
                $existing_version = new ModuleVersionItem();
                $existing_version->name = $name;
            }

            //	Check Create
            if ($create) {

                //	Install Version Number
                $existing_version->addVersionLog($version);
            }

            //	Return
            return false;
        }

        //	Return
        return true;
    }

    public function versionRequiresUpdate($version) {
        if ($version > $this->version)
            return true;
        return false;
    }

    public function addVersionLog($version) {
        return ModuleVersionItem::insert(array(
            "name" => $this->name,
            "version" => $version,
            "installed_on" => date("Y-m-d H:i:s")
        ));
    }

    public static function removeVersionLog($name, $version) {
        ModuleVersionItem::where("name", $name)->where("version", $version)->delete();
    }
}
